<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Jobs\ProcessLeadResearch;
use App\Jobs\DispatchWhatsAppMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /**
     * Show dashboard home.
     */
    public function index()
    {
        $leads = Lead::orderBy('created_at', 'desc')->get();
        $services = \App\Models\Service::all();

        $stats = [
            'total' => $leads->count(),
            'pending' => $leads->where('status', 'pending')->count(),
            'researching' => $leads->where('status', 'researching')->count(),
            'compressing' => $leads->where('status', 'compressing')->count(),
            'drafting' => $leads->where('status', 'drafting')->count(),
            'queued' => $leads->where('status', 'queued')->count(),
            'sent' => $leads->where('status', 'sent')->count(),
            'failed' => $leads->where('status', 'failed')->count(),
        ];

        return view('dashboard', compact('leads', 'stats', 'services'));
    }

    /**
     * Handle CSV upload and import.
     */
    public function uploadCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        try {
            $file = $request->file('csv_file');
            $filePath = $file->getRealPath();

            $handle = fopen($filePath, 'r');
            if (!$handle) {
                return back()->with('error', 'Unable to open uploaded file.');
            }

            // Parse headers
            $headers = fgetcsv($handle, 1000, ',');
            if (!$headers) {
                fclose($handle);
                return back()->with('error', 'Empty CSV file uploaded.');
            }

            // Map header indices case-insensitively
            $companyNameIdx = -1;
            $websiteUrlIdx = -1;
            $phoneIdx = -1;

            foreach ($headers as $index => $header) {
                $header = strtolower(trim($header));
                if (str_contains($header, 'web') || str_contains($header, 'url') || str_contains($header, 'site')) {
                    $websiteUrlIdx = $index;
                } elseif (str_contains($header, 'company') || str_contains($header, 'name')) {
                    $companyNameIdx = $index;
                } elseif (str_contains($header, 'phone') || str_contains($header, 'number') || str_contains($header, 'mobile')) {
                    $phoneIdx = $index;
                }
            }

            // Fallbacks if headers didn't match perfectly
            if ($companyNameIdx === -1) $companyNameIdx = 0;
            if ($websiteUrlIdx === -1) $websiteUrlIdx = 1;
            if ($phoneIdx === -1) $phoneIdx = 2;

            $imported = 0;

            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                if (count($row) <= max($companyNameIdx, $websiteUrlIdx, $phoneIdx)) {
                    continue; // skip malformed row
                }

                $companyName = trim($row[$companyNameIdx] ?? '');
                $websiteUrl = trim($row[$websiteUrlIdx] ?? '');
                $phone = trim($row[$phoneIdx] ?? '');

                if (empty($companyName) || empty($phone)) {
                    continue; // Skip rows lacking name or phone
                }

                // Create lead
                $lead = Lead::create([
                    'company_name' => $companyName,
                    'website_url' => $websiteUrl,
                    'phone_number' => $phone,
                    'status' => 'pending'
                ]);

                // Dispatch process research job
                ProcessLeadResearch::dispatch($lead);
                $imported++;
            }

            fclose($handle);

            return back()->with('success', "Successfully imported {$imported} leads and queued research jobs.");
        } catch (\Exception $e) {
            Log::error("CSV import failed: " . $e->getMessage());
            return back()->with('error', "Import error: " . $e->getMessage());
        }
    }

    /**
     * Update generated copy of the lead.
     */
    public function updateCopy(Request $request, Lead $lead)
    {
        $request->validate([
            'generated_copy' => 'required|string',
        ]);

        $lead->update([
            'generated_copy' => $request->input('generated_copy'),
        ]);

        return back()->with('success', "Updated message copy for {$lead->company_name}.");
    }

    /**
     * Manually send WhatsApp message for the lead.
     */
    public function sendLead(Lead $lead)
    {
        if (empty(trim($lead->generated_copy))) {
            return back()->with('error', "Cannot send message: no generated copy exists.");
        }

        $lead->update([
            'status' => 'queued',
            'error_logs' => null,
        ]);

        DispatchWhatsAppMessage::dispatch($lead);

        return back()->with('success', "Dispatched WhatsApp message for {$lead->company_name} to dedicated queue.");
    }

    /**
     * Retry research/outreach for a failed lead.
     */
    public function retryLead(Lead $lead)
    {
        $lead->update([
            'status' => 'pending',
            'error_logs' => null
        ]);

        ProcessLeadResearch::dispatch($lead);

        return back()->with('success', "Queued retry for lead: {$lead->company_name}");
    }

    /**
     * Clear all leads (for debugging).
     */
    public function resetLeads()
    {
        Lead::truncate();
        return back()->with('success', 'All leads cleared successfully.');
    }

    /**
     * Get the real-time status of all microservices in JSON format.
     */
    public function getServicesStatus()
    {
        $services = \App\Models\Service::all();
        $statusMap = [];

        foreach ($services as $service) {
            $isOnline = $this->pingPort('127.0.0.1', $service->port);
            
            $statusMap[] = [
                'id' => $service->id,
                'key' => $service->key,
                'name' => $service->name,
                'port' => $service->port,
                'description' => $service->description,
                'startup_command' => $service->startup_command,
                'shutdown_command' => $service->shutdown_command,
                'online' => $isOnline,
            ];
        }

        return response()->json($statusMap);
    }

    /**
     * Toggle a service state (start/stop).
     */
    public function toggleService(Request $request, \App\Models\Service $service)
    {
        $action = $request->input('action'); // 'start' or 'stop'

        if ($action === 'start') {
            Log::info("Starting service {$service->name} with command: {$service->startup_command}");
            
            // Execute in background on Windows
            pclose(popen("start /B " . $service->startup_command, "r"));
            
            return response()->json([
                'success' => true,
                'message' => "Service {$service->name} startup command executed.",
            ]);
        } elseif ($action === 'stop') {
            Log::info("Stopping service {$service->name}");

            // Run shutdown command if exists
            if ($service->shutdown_command) {
                Log::info("Running shutdown command: {$service->shutdown_command}");
                @shell_exec($service->shutdown_command);
            }

            // Also kill process on its port to ensure it stops
            $this->killProcessByPort($service->port);

            return response()->json([
                'success' => true,
                'message' => "Service {$service->name} stopped successfully.",
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Invalid action.'], 400);
    }

    /**
     * Update service configuration.
     */
    public function updateServiceSettings(Request $request, \App\Models\Service $service)
    {
        $request->validate([
            'port' => 'required|integer',
            'startup_command' => 'required|string',
            'shutdown_command' => 'nullable|string',
        ]);

        $service->update([
            'port' => $request->input('port'),
            'startup_command' => $request->input('startup_command'),
            'shutdown_command' => $request->input('shutdown_command'),
        ]);

        return back()->with('success', "Settings updated for {$service->name}.");
    }

    /**
     * Ping port to check status.
     */
    private function pingPort(string $host, int $port, int $timeout = 1): bool
    {
        $connection = @fsockopen($host, $port, $errno, $errstr, $timeout);
        if (is_resource($connection)) {
            fclose($connection);
            return true;
        }
        return false;
    }

    /**
     * Kill process by port.
     */
    private function killProcessByPort(int $port): void
    {
        $cmd = "for /f \"tokens=5\" %a in ('netstat -aon ^| findstr :{$port}') do taskkill /F /PID %a";
        @shell_exec($cmd);
    }
}
