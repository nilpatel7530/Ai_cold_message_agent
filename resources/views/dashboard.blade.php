<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Flaunt Team | B2B Outreach Pipeline</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=Plus+Jakarta+Sans:wght@300;400;500;700&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        outfit: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        darkBg: '#090b11',
                        cardBg: 'rgba(17, 20, 36, 0.75)',
                        borderBg: 'rgba(255, 255, 255, 0.08)',
                        accentPrimary: '#6366f1', // Indigo
                        accentSecondary: '#a855f7', // Purple
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #090b11;
            background-image: 
                radial-gradient(circle at 12% 18%, rgba(99, 102, 241, 0.12) 0%, transparent 45%),
                radial-gradient(circle at 88% 82%, rgba(168, 85, 247, 0.1) 0%, transparent 45%);
            background-attachment: fixed;
        }
    </style>
</head>
<body class="font-sans text-slate-200 min-h-screen py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        
        <!-- Header -->
        <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b border-white/10 pb-6 mb-8 gap-4">
            <div>
                <h1 class="font-outfit text-3xl font-extrabold tracking-tight bg-gradient-to-r from-white via-indigo-200 to-purple-400 bg-clip-text text-transparent drop-shadow-md">
                    THE FLAUNT TEAM
                </h1>
                <p class="text-xs font-semibold tracking-widest text-slate-400 uppercase mt-1">
                    B2B Lead Enrichment & WhatsApp Outreach
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="/horizon" target="_blank" class="bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-400 hover:text-indigo-300 border border-indigo-500/25 px-4 py-2 rounded-lg text-xs font-semibold tracking-wide transition duration-200 flex items-center gap-1.5 shadow-lg shadow-indigo-500/5">
                    ⚙️ Queue Dashboard
                </a>
                <form action="{{ route('leads.reset') }}" method="POST" onsubmit="return confirm('Delete all leads from database?');">
                    @csrf
                    <button type="submit" class="bg-red-500/10 hover:bg-red-500/20 text-red-400 hover:text-red-300 border border-red-500/25 px-4 py-2 rounded-lg text-xs font-semibold tracking-wide transition duration-200">
                        Clear Database
                    </button>
                </form>
            </div>
        </header>

        <!-- Sessions Alerts -->
        @if (session('success'))
            <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-4 py-3 rounded-xl mb-6 font-medium text-sm flex items-center gap-2">
                <span>✅</span> {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-xl mb-6 font-medium text-sm flex items-center gap-2">
                <span>⚠️</span> {{ session('error') }}
            </div>
        @endif

        <!-- Stats Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-4 mb-8">
            <!-- Total -->
            <div class="bg-cardBg backdrop-blur-md border border-white/5 rounded-xl p-4 flex flex-col justify-between">
                <span class="text-[10px] font-bold tracking-wider text-slate-400 uppercase">Total</span>
                <span class="font-outfit text-2xl font-bold text-white mt-2">{{ $stats['total'] }}</span>
            </div>
            <!-- Pending -->
            <div class="bg-cardBg backdrop-blur-md border border-white/5 border-l-amber-500 border-l-2 rounded-xl p-4 flex flex-col justify-between">
                <span class="text-[10px] font-bold tracking-wider text-amber-400 uppercase">Pending</span>
                <span class="font-outfit text-2xl font-bold text-white mt-2">{{ $stats['pending'] }}</span>
            </div>
            <!-- Researching -->
            <div class="bg-cardBg backdrop-blur-md border border-white/5 border-l-blue-500 border-l-2 rounded-xl p-4 flex flex-col justify-between">
                <span class="text-[10px] font-bold tracking-wider text-blue-400 uppercase">Researching</span>
                <span class="font-outfit text-2xl font-bold text-white mt-2">{{ $stats['researching'] }}</span>
            </div>
            <!-- Compressing -->
            <div class="bg-cardBg backdrop-blur-md border border-white/5 border-l-teal-500 border-l-2 rounded-xl p-4 flex flex-col justify-between">
                <span class="text-[10px] font-bold tracking-wider text-teal-400 uppercase">Compressing</span>
                <span class="font-outfit text-2xl font-bold text-white mt-2">{{ $stats['compressing'] }}</span>
            </div>
            <!-- Drafting -->
            <div class="bg-cardBg backdrop-blur-md border border-white/5 border-l-pink-500 border-l-2 rounded-xl p-4 flex flex-col justify-between">
                <span class="text-[10px] font-bold tracking-wider text-pink-400 uppercase">Drafting</span>
                <span class="font-outfit text-2xl font-bold text-white mt-2">{{ $stats['drafting'] }}</span>
            </div>
            <!-- Queued -->
            <div class="bg-cardBg backdrop-blur-md border border-white/5 border-l-indigo-500 border-l-2 rounded-xl p-4 flex flex-col justify-between">
                <span class="text-[10px] font-bold tracking-wider text-indigo-400 uppercase">Queued</span>
                <span class="font-outfit text-2xl font-bold text-white mt-2">{{ $stats['queued'] }}</span>
            </div>
            <!-- Sent -->
            <div class="bg-cardBg backdrop-blur-md border border-white/5 border-l-emerald-500 border-l-2 rounded-xl p-4 flex flex-col justify-between">
                <span class="text-[10px] font-bold tracking-wider text-emerald-400 uppercase">Sent</span>
                <span class="font-outfit text-2xl font-bold text-white mt-2">{{ $stats['sent'] }}</span>
            </div>
            <!-- Failed -->
            <div class="bg-cardBg backdrop-blur-md border border-white/5 border-l-red-500 border-l-2 rounded-xl p-4 flex flex-col justify-between">
                <span class="text-[10px] font-bold tracking-wider text-red-400 uppercase">Failed</span>
                <span class="font-outfit text-2xl font-bold text-white mt-2">{{ $stats['failed'] }}</span>
            </div>
        </div>

        <!-- Main Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            
            <!-- Left panel: Ingest & Statuses -->
            <div class="lg:col-span-1 space-y-6">
                <!-- CSV Upload -->
                <div class="bg-cardBg backdrop-blur-md border border-white/10 rounded-2xl p-6 shadow-xl">
                    <h3 class="font-outfit text-lg font-bold text-white mb-4 flex items-center gap-2">
                        📥 Ingest Leads
                    </h3>
                    <form action="{{ route('upload.csv') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="border-2 border-dashed border-white/15 hover:border-indigo-500/50 rounded-xl p-6 text-center cursor-pointer transition relative bg-white/[0.01]">
                            <span class="text-3xl block mb-2">📄</span>
                            <span class="text-xs font-bold text-slate-300 block mb-1">Select Lead CSV File</span>
                            <span class="text-[10px] text-slate-400 block">Required headers: company_name, website_url, phone_number</span>
                            <input type="file" name="csv_file" accept=".csv" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" onchange="this.form.submit()">
                        </div>
                        <button type="submit" class="w-full mt-4 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white font-semibold text-xs py-3 rounded-lg shadow-lg shadow-indigo-500/20 transition duration-200">
                            Upload & Process
                        </button>
                    </form>
                </div>

                <!-- Service Ports status indicator and Controller Console -->
                <div class="bg-cardBg backdrop-blur-md border border-white/10 rounded-2xl p-6 shadow-xl">
                    <h3 class="font-outfit text-sm font-bold text-white mb-4 uppercase tracking-wider flex items-center justify-between">
                        <span>🔌 Microservice Console</span>
                        <span class="text-[9px] text-slate-400 normal-case font-mono tracking-normal">Real-time status</span>
                    </h3>
                    
                    <div class="space-y-4">
                        @foreach ($services as $service)
                            <div class="bg-white/[0.02] border border-white/5 p-3.5 rounded-xl space-y-2.5 transition duration-250 hover:bg-white/[0.04] transition-all" id="service-card-{{ $service->key }}">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <!-- Realtime status indicator dot -->
                                        <span class="h-2 w-2 rounded-full bg-red-500 shadow-lg shadow-red-500/50" id="status-dot-{{ $service->key }}"></span>
                                        <span class="text-xs font-bold text-slate-200">{{ $service->name }}</span>
                                    </div>
                                    <span class="text-[9px] font-mono bg-white/5 px-1.5 py-0.5 rounded text-slate-400" id="port-badge-{{ $service->key }}">
                                        Port {{ $service->port }}
                                    </span>
                                </div>
                                <p class="text-[10px] text-slate-400 leading-relaxed">{{ $service->description }}</p>
                                
                                <div class="flex items-center justify-between pt-1 border-t border-white/5">
                                    <!-- Edit settings button -->
                                    <button type="button" onclick="openSettingsModal('{{ $service->id }}', '{{ $service->name }}', '{{ $service->port }}', '{{ addslashes($service->startup_command) }}', '{{ addslashes($service->shutdown_command) }}')" class="text-[10px] text-slate-400 hover:text-indigo-300 flex items-center gap-1 transition">
                                        ⚙️ Settings
                                    </button>

                                    <!-- Switch Toggle -->
                                    <div class="flex items-center gap-2">
                                        <span class="text-[9px] font-bold tracking-wide text-slate-400" id="toggle-label-{{ $service->key }}">OFF</span>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" id="toggle-{{ $service->key }}" onchange="toggleService('{{ $service->id }}', '{{ $service->key }}', this)" class="sr-only peer">
                                            <div class="w-7 h-4 bg-slate-850 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-slate-400 after:border-slate-300 after:border after:rounded-full after:h-3 after:w-3 after:transition-all peer-checked:bg-indigo-600 peer-checked:after:bg-white peer-checked:after:border-white"></div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Right panel: Lead Pipeline Table -->
            <div class="lg:col-span-3">
                <div class="bg-cardBg backdrop-blur-md border border-white/10 rounded-2xl p-6 shadow-xl hover:shadow-2xl hover:shadow-indigo-500/[0.02] transition-all duration-300">
                    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
                        <h3 class="font-outfit text-xl font-bold text-white flex items-center gap-2">
                            ⏳ Outreach Queue
                        </h3>
                        
                        @if (!$leads->isEmpty())
                            <!-- Filtering Tabs -->
                            <div class="flex flex-wrap gap-1.5 text-xs bg-slate-950/45 p-1.5 rounded-xl border border-white/5">
                                <button type="button" onclick="filterQueue('all')" id="tab-all" class="px-3 py-1.5 rounded-lg bg-indigo-650 text-white font-bold transition shadow-lg shadow-indigo-550/20">
                                    All ({{ $leads->count() }})
                                </button>
                                <button type="button" onclick="filterQueue('pending')" id="tab-pending" class="px-3 py-1.5 rounded-lg text-slate-400 hover:text-slate-200 transition">
                                    Pending ({{ $leads->where('status', 'pending')->count() }})
                                </button>
                                <button type="button" onclick="filterQueue('processing')" id="tab-processing" class="px-3 py-1.5 rounded-lg text-slate-400 hover:text-slate-200 transition">
                                    Processing ({{ $leads->whereIn('status', ['researching', 'compressing', 'drafting'])->count() }})
                                </button>
                                <button type="button" onclick="filterQueue('queued')" id="tab-queued" class="px-3 py-1.5 rounded-lg text-slate-400 hover:text-slate-200 transition">
                                    Queued ({{ $leads->where('status', 'queued')->count() }})
                                </button>
                                <button type="button" onclick="filterQueue('sent')" id="tab-sent" class="px-3 py-1.5 rounded-lg text-slate-400 hover:text-slate-200 transition">
                                    Sent ({{ $leads->where('status', 'sent')->count() }})
                                </button>
                                <button type="button" onclick="filterQueue('failed')" id="tab-failed" class="px-3 py-1.5 rounded-lg text-slate-400 hover:text-slate-200 transition">
                                    Failed ({{ $leads->where('status', 'failed')->count() }})
                                </button>
                            </div>
                        @endif
                    </div>

                    @if ($leads->isEmpty())
                        <div class="text-center py-20">
                            <span class="text-5xl block mb-4">🛸</span>
                            <h4 class="text-sm font-bold text-white mb-1">Queue is empty</h4>
                            <p class="text-xs text-slate-400">Import a CSV containing leads to start the autonomous flow.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse">
                                <thead>
                                    <tr class="border-b border-white/10 text-left">
                                        <th class="py-3 px-4 text-[10px] font-bold tracking-wider text-slate-400 uppercase">Lead Name & URL</th>
                                        <th class="py-3 px-4 text-[10px] font-bold tracking-wider text-slate-400 uppercase">Contact</th>
                                        <th class="py-3 px-4 text-[10px] font-bold tracking-wider text-slate-400 uppercase">Status & Pipeline Progress</th>
                                        <th class="py-3 px-4 text-[10px] font-bold tracking-wider text-slate-400 uppercase">Enrichment & Copy Editor</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/5">
                                    @foreach ($leads as $lead)
                                        <tr class="hover:bg-white/[0.01] lead-row transition-all duration-200" data-status="{{ $lead->status }}">
                                            <!-- Name & URL -->
                                            <td class="py-4 px-4">
                                                <div class="font-bold text-sm text-white">{{ $lead->company_name }}</div>
                                                @if ($lead->website_url)
                                                    <a href="{{ $lead->website_url }}" target="_blank" class="text-xs text-indigo-400 hover:text-indigo-300 hover:underline inline-block mt-0.5">
                                                        {{ Str::limit($lead->website_url, 30) }} 🔗
                                                    </a>
                                                @endif
                                            </td>
                                            
                                            <!-- Contact -->
                                            <td class="py-4 px-4">
                                                <span class="font-mono text-xs text-slate-300">{{ $lead->phone_number }}</span>
                                            </td>

                                            <!-- Status -->
                                            <td class="py-4 px-4">
                                                @php
                                                    $status = $lead->status;
                                                    $stage1 = 'inactive'; // Research
                                                    $stage2 = 'inactive'; // Optimize
                                                    $stage3 = 'inactive'; // Draft
                                                    $stage4 = 'inactive'; // Queue
                                                    $stage5 = 'inactive'; // Send

                                                    if ($status === 'researching') {
                                                        $stage1 = 'active-blue';
                                                    } elseif ($status === 'compressing') {
                                                        $stage1 = 'completed';
                                                        $stage2 = 'active-teal';
                                                    } elseif ($status === 'drafting') {
                                                        $stage1 = 'completed';
                                                        $stage2 = 'completed';
                                                        $stage3 = 'active-pink';
                                                    } elseif ($status === 'queued') {
                                                        $stage1 = 'completed';
                                                        $stage2 = 'completed';
                                                        $stage3 = 'completed';
                                                        $stage4 = 'active-indigo';
                                                    } elseif ($status === 'sending') {
                                                        $stage1 = 'completed';
                                                        $stage2 = 'completed';
                                                        $stage3 = 'completed';
                                                        $stage4 = 'completed';
                                                        $stage5 = 'active-purple';
                                                    } elseif ($status === 'sent') {
                                                        $stage1 = 'completed';
                                                        $stage2 = 'completed';
                                                        $stage3 = 'completed';
                                                        $stage4 = 'completed';
                                                        $stage5 = 'completed';
                                                    } elseif ($status === 'failed') {
                                                        if (empty($lead->raw_research_data)) {
                                                            $stage1 = 'failed';
                                                        } elseif (empty($lead->compressed_context)) {
                                                            $stage1 = 'completed';
                                                            $stage2 = 'failed';
                                                        } elseif (empty($lead->generated_copy)) {
                                                            $stage1 = 'completed';
                                                            $stage2 = 'completed';
                                                            $stage3 = 'failed';
                                                        } else {
                                                            $stage1 = 'completed';
                                                            $stage2 = 'completed';
                                                            $stage3 = 'completed';
                                                            $stage4 = 'completed';
                                                            $stage5 = 'failed';
                                                        }
                                                    }

                                                    $statusColors = [
                                                        'pending' => 'bg-amber-500/10 border-amber-500/30 text-amber-400',
                                                        'researching' => 'bg-blue-500/10 border-blue-500/30 text-blue-400',
                                                        'compressing' => 'bg-teal-500/10 border-teal-500/30 text-teal-400',
                                                        'drafting' => 'bg-pink-500/10 border-pink-500/30 text-pink-400',
                                                        'queued' => 'bg-indigo-500/10 border-indigo-500/30 text-indigo-400',
                                                        'sending' => 'bg-purple-500/10 border-purple-500/30 text-purple-400 animate-pulse',
                                                        'sent' => 'bg-emerald-500/10 border-emerald-500/30 text-emerald-400',
                                                        'failed' => 'bg-red-500/10 border-red-500/30 text-red-400',
                                                    ];
                                                    $color = $statusColors[$status] ?? 'bg-slate-500/10 border-slate-500/30 text-slate-400';
                                                @endphp
                                                
                                                <div class="flex flex-col gap-1.5 select-none">
                                                    <!-- Visual progress stepper -->
                                                    <div class="flex items-center gap-1 text-[9px] bg-slate-950/30 p-1.5 rounded-lg border border-white/5 w-max">
                                                        <!-- Stage 1 -->
                                                        <div class="flex items-center gap-0.5">
                                                            @if ($stage1 === 'completed')
                                                                <span class="text-emerald-500 font-bold" title="Research Completed">✓</span>
                                                            @elseif ($stage1 === 'active-blue')
                                                                <span class="h-1.5 w-1.5 rounded-full bg-blue-505 animate-ping mr-1" title="Researching..."></span>
                                                            @elseif ($stage1 === 'failed')
                                                                <span class="text-red-550 font-bold" title="Research Failed">✗</span>
                                                            @else
                                                                <span class="h-1 w-1 rounded-full bg-white/20 mr-1" title="Research Pending"></span>
                                                            @endif
                                                            <span class="{{ $stage1 === 'completed' ? 'text-emerald-400' : ($stage1 === 'active-blue' ? 'text-blue-400 font-bold animate-pulse' : ($stage1 === 'failed' ? 'text-red-400 font-bold animate-pulse' : 'text-slate-600')) }}">Scrape</span>
                                                        </div>
                                                        
                                                        <span class="text-white/10 mx-0.5">➔</span>

                                                        <!-- Stage 2 -->
                                                        <div class="flex items-center gap-0.5">
                                                            @if ($stage2 === 'completed')
                                                                <span class="text-emerald-500 font-bold" title="Compression Completed">✓</span>
                                                            @elseif ($stage2 === 'active-teal')
                                                                <span class="h-1.5 w-1.5 rounded-full bg-teal-400 animate-ping mr-1" title="Compressing..."></span>
                                                            @elseif ($stage2 === 'failed')
                                                                <span class="text-red-500 font-bold" title="Compression Failed">✗</span>
                                                            @else
                                                                <span class="h-1 w-1 rounded-full bg-white/20 mr-1" title="Compression Pending"></span>
                                                            @endif
                                                            <span class="{{ $stage2 === 'completed' ? 'text-emerald-400' : ($stage2 === 'active-teal' ? 'text-teal-400 font-bold animate-pulse' : ($stage2 === 'failed' ? 'text-red-400 font-bold animate-pulse' : 'text-slate-600')) }}">Crush</span>
                                                        </div>

                                                        <span class="text-white/10 mx-0.5">➔</span>

                                                        <!-- Stage 3 -->
                                                        <div class="flex items-center gap-0.5">
                                                            @if ($stage3 === 'completed')
                                                                <span class="text-emerald-500 font-bold" title="Generation Completed">✓</span>
                                                            @elseif ($stage3 === 'active-pink')
                                                                <span class="h-1.5 w-1.5 rounded-full bg-pink-500 animate-ping mr-1" title="Generating Copy..."></span>
                                                            @elseif ($stage3 === 'failed')
                                                                <span class="text-red-500 font-bold" title="Generation Failed">✗</span>
                                                            @else
                                                                <span class="h-1 w-1 rounded-full bg-white/20 mr-1" title="Generation Pending"></span>
                                                            @endif
                                                            <span class="{{ $stage3 === 'completed' ? 'text-emerald-400' : ($stage3 === 'active-pink' ? 'text-pink-400 font-bold animate-pulse' : ($stage3 === 'failed' ? 'text-red-400 font-bold animate-pulse' : 'text-slate-600')) }}">Gen</span>
                                                        </div>

                                                        <span class="text-white/10 mx-0.5">➔</span>

                                                        <!-- Stage 4 -->
                                                        <div class="flex items-center gap-0.5">
                                                            @if ($stage4 === 'completed')
                                                                <span class="text-emerald-500 font-bold" title="Queue Completed">✓</span>
                                                            @elseif ($stage4 === 'active-indigo')
                                                                <span class="h-1.5 w-1.5 rounded-full bg-indigo-500 animate-ping mr-1" title="Queued..."></span>
                                                            @elseif ($stage4 === 'failed')
                                                                <span class="text-red-500 font-bold" title="Queue Failed">✗</span>
                                                            @else
                                                                <span class="h-1 w-1 rounded-full bg-white/20 mr-1" title="Queue Pending"></span>
                                                            @endif
                                                            <span class="{{ $stage4 === 'completed' ? 'text-emerald-400' : ($stage4 === 'active-indigo' ? 'text-indigo-400 font-bold animate-pulse' : ($stage4 === 'failed' ? 'text-red-400 font-bold animate-pulse' : 'text-slate-600')) }}">Queue</span>
                                                        </div>

                                                        <span class="text-white/10 mx-0.5">➔</span>

                                                        <!-- Stage 5 -->
                                                        <div class="flex items-center gap-0.5">
                                                            @if ($stage5 === 'completed')
                                                                <span class="text-emerald-400 font-bold" title="Sent Successfully">📬 Sent</span>
                                                            @elseif ($stage5 === 'active-purple')
                                                                <span class="h-1.5 w-1.5 rounded-full bg-purple-500 animate-ping mr-1" title="Sending..."></span>
                                                                <span class="text-purple-400 font-bold animate-pulse">Sending</span>
                                                            @elseif ($stage5 === 'failed')
                                                                <span class="text-red-500 font-bold" title="Send Failed">✗ Failed</span>
                                                            @else
                                                                <span class="h-1 w-1 rounded-full bg-white/20 mr-1" title="Outreach Pending"></span>
                                                                <span class="text-slate-600">Send</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    
                                                    <div>
                                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider border {{ $color }}">
                                                            {{ $status }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Enrichment & Copy Editor -->
                                            <td class="py-4 px-4 text-xs">
                                                @if (in_array($lead->status, ['drafting', 'queued', 'failed', 'sent', 'sending']))
                                                    <div class="space-y-2.5">
                                                        <!-- Live Edit Copy Form -->
                                                        <form action="{{ route('leads.update_copy', $lead) }}" method="POST" class="space-y-1.5 max-w-lg">
                                                            @csrf
                                                            <div class="relative">
                                                                <textarea name="generated_copy" rows="3" oninput="updateCharCount('{{ $lead->id }}', this)" class="w-full bg-slate-950/60 border border-white/10 rounded-lg p-2.5 text-xs text-slate-200 focus:outline-none focus:border-indigo-500 resize-y transition-colors duration-200" placeholder="Awaiting message generation..." {{ in_array($lead->status, ['sent', 'sending']) ? 'disabled' : '' }}>{{ $lead->generated_copy }}</textarea>
                                                            </div>
                                                            
                                                            <div class="flex items-center justify-between">
                                                                <span class="text-[9px] text-slate-500 font-mono" id="char-counter-{{ $lead->id }}">{{ strlen($lead->generated_copy ?? '') }} chars</span>
                                                                <div class="flex gap-2">
                                                                    @if ($lead->generated_copy)
                                                                        <button type="button" onclick="copyToClipboard(this.form.generated_copy.value, this)" class="bg-white/5 hover:bg-white/10 text-slate-300 border border-white/10 px-2.5 py-1 rounded text-[10px] font-semibold transition flex items-center gap-1">
                                                                            📋 Copy
                                                                        </button>
                                                                    @endif
                                                                    @if (!in_array($lead->status, ['sent', 'sending']))
                                                                        <button type="submit" class="bg-indigo-650/15 hover:bg-indigo-650/30 text-indigo-400 border border-indigo-500/20 px-2.5 py-1 rounded text-[10px] font-semibold transition">
                                                                            Save Copy
                                                                        </button>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </form>

                                                        <!-- Action Buttons -->
                                                        <div class="flex flex-wrap items-center gap-2">
                                                            @if (in_array($lead->status, ['drafting', 'failed']))
                                                                <form action="{{ route('leads.send', $lead) }}" method="POST">
                                                                    @csrf
                                                                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-755 text-white px-3 py-1.5 rounded text-[10px] font-bold transition flex items-center gap-1 shadow-lg shadow-indigo-500/10">
                                                                        🚀 Dispatch WhatsApp
                                                                    </button>
                                                                </form>
                                                            @endif

                                                            @if ($lead->status === 'queued')
                                                                <span class="text-[10px] text-indigo-300 font-semibold flex items-center gap-1 bg-indigo-500/10 border border-indigo-500/20 px-2.5 py-1 rounded-lg">
                                                                    ⏳ Anti-ban Queue active (Jitter 60-180s)
                                                                </span>
                                                            @endif

                                                            @if ($lead->status === 'failed')
                                                                <form action="{{ route('leads.retry', $lead) }}" method="POST">
                                                                    @csrf
                                                                    <button type="submit" class="bg-red-500/10 hover:bg-red-500/20 text-red-300 border border-red-500/20 px-3 py-1.5 rounded text-[10px] font-semibold transition">
                                                                        🔄 Retry Pipeline
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Errors log area -->
                                                    @if ($lead->status === 'failed' && $lead->error_logs)
                                                        <details class="group mt-2.5 max-w-lg">
                                                            <summary class="text-[10px] text-red-400 hover:text-red-305 cursor-pointer font-bold select-none list-none flex items-center gap-1">
                                                                <span class="transition-transform group-open:rotate-90">▶</span>
                                                                View Error Logs
                                                            </summary>
                                                            <div class="mt-2 p-3 bg-red-950/20 border border-red-500/20 text-red-350 rounded-lg font-mono text-[10px] whitespace-pre-wrap max-h-32 overflow-y-auto shadow-inner">
                                                                <strong>Error Stack:</strong><br>{{ $lead->error_logs }}
                                                            </div>
                                                        </details>
                                                    @endif
                                                @else
                                                    <!-- States where processing hasn't finished draft yet -->
                                                    <div class="flex items-center gap-2 text-slate-400 bg-white/[0.01] p-3 rounded-lg border border-white/5">
                                                        <svg class="animate-spin h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24">
                                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                        </svg>
                                                        @if ($lead->status === 'pending')
                                                            <span>Enqueued in pipeline...</span>
                                                        @elseif ($lead->status === 'researching')
                                                            <span>Researching target via SearXNG...</span>
                                                        @elseif ($lead->status === 'compressing')
                                                            <span>Running Headroom SmartCrusher...</span>
                                                        @endif
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

        </div>

    </div>

    <!-- Settings Modal -->
    <div id="settings-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black/60 backdrop-blur-sm px-4">
        <div class="bg-slate-900 border border-white/10 rounded-2xl max-w-md w-full p-6 shadow-2xl relative text-left">
            <h3 class="font-outfit text-lg font-bold text-white mb-2" id="modal-service-name">Service Settings</h3>
            <p class="text-xs text-slate-400 mb-6 font-sans">Modify the startup or shutdown scripts for this service to match your machine setup.</p>
            
            <form id="modal-settings-form" method="POST" action="">
                @csrf
                <div class="space-y-4 text-xs font-sans">
                    <div>
                        <label class="block font-bold text-slate-300 mb-1">Port Number</label>
                        <input type="number" name="port" id="modal-input-port" required class="w-full bg-slate-950 border border-white/10 rounded-lg p-2.5 text-slate-200 focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-300 mb-1">Startup Command</label>
                        <textarea name="startup_command" id="modal-input-startup" rows="3" required class="w-full bg-slate-950 border border-white/10 rounded-lg p-2.5 text-slate-200 focus:outline-none focus:border-indigo-500 font-mono"></textarea>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-300 mb-1">Shutdown Command (Optional)</label>
                        <textarea name="shutdown_command" id="modal-input-shutdown" rows="2" class="w-full bg-slate-950 border border-white/10 rounded-lg p-2.5 text-slate-200 focus:outline-none focus:border-indigo-500 font-mono" placeholder="Will kill by port process if empty"></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6 text-xs font-sans">
                    <button type="button" onclick="closeSettingsModal()" class="bg-white/5 hover:bg-white/10 text-white font-semibold px-4 py-2.5 rounded-lg transition">
                        Cancel
                    </button>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-5 py-2.5 rounded-lg transition shadow-lg shadow-indigo-500/20">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Copy to clipboard helper
        function copyToClipboard(text, btn) {
            if (!text) return;
            navigator.clipboard.writeText(text).then(() => {
                const originalText = btn.innerHTML;
                btn.innerHTML = "📋 Copied!";
                btn.className = "bg-emerald-500/20 text-emerald-305 border border-emerald-500/30 px-2.5 py-1 rounded text-[10px] font-semibold transition";
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.className = "bg-white/5 hover:bg-white/10 text-slate-300 border border-white/10 px-2.5 py-1 rounded text-[10px] font-semibold transition";
                }, 2000);
            }).catch(err => {
                console.error('Failed to copy text: ', err);
            });
        }

        // Live character counter helper
        function updateCharCount(leadId, textarea) {
            const counter = document.getElementById(`char-counter-${leadId}`);
            if (counter) {
                counter.innerText = textarea.value.length + ' chars';
            }
        }

        // Filter lead rows by status tab
        function filterQueue(status) {
            const tabs = ['all', 'pending', 'processing', 'queued', 'sent', 'failed'];
            tabs.forEach(t => {
                const btn = document.getElementById(`tab-${t}`);
                if (btn) {
                    if (t === status) {
                        btn.className = "px-3 py-1.5 rounded-lg bg-indigo-650 text-white font-bold transition shadow-lg shadow-indigo-550/20";
                    } else {
                        btn.className = "px-3 py-1.5 rounded-lg text-slate-400 hover:text-slate-200 transition";
                    }
                }
            });

            const rows = document.querySelectorAll('.lead-row');
            rows.forEach(row => {
                const leadStatus = row.getAttribute('data-status');
                if (status === 'all') {
                    row.classList.remove('hidden');
                } else if (status === 'processing') {
                    if (['researching', 'compressing', 'drafting', 'sending'].includes(leadStatus)) {
                        row.classList.remove('hidden');
                    } else {
                        row.classList.add('hidden');
                    }
                } else {
                    if (leadStatus === status) {
                        row.classList.remove('hidden');
                    } else {
                        row.classList.add('hidden');
                    }
                }
            });
        }

        // Modal functions
        function openSettingsModal(id, name, port, startup, shutdown) {
            const modal = document.getElementById('settings-modal');
            const form = document.getElementById('modal-settings-form');
            
            document.getElementById('modal-service-name').innerText = name + ' Settings';
            document.getElementById('modal-input-port').value = port;
            document.getElementById('modal-input-startup').value = startup;
            document.getElementById('modal-input-shutdown').value = shutdown || '';
            
            // Set form action route
            form.action = `/services/update/${id}`;
            
            modal.classList.remove('hidden');
        }

        function closeSettingsModal() {
            document.getElementById('settings-modal').classList.add('hidden');
        }

        // Toggle service startup/shutdown
        async function toggleService(id, key, checkbox) {
            const isChecked = checkbox.checked;
            const action = isChecked ? 'start' : 'stop';
            const label = document.getElementById(`toggle-label-${key}`);
            const dot = document.getElementById(`status-dot-${key}`);
            
            // Temporarily update label and dot to show loading
            label.innerText = isChecked ? 'STARTING...' : 'STOPPING...';
            label.className = "text-[9px] font-bold tracking-wide text-indigo-400 animate-pulse";
            dot.className = "h-2 w-2 rounded-full bg-indigo-500 animate-ping shadow-lg shadow-indigo-500/50";

            try {
                const response = await fetch(`/services/toggle/${id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ action: action })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Update state after short buffer to allow port bindings to update
                    setTimeout(fetchStatus, 2000);
                } else {
                    alert(result.message || 'Action failed.');
                    checkbox.checked = !isChecked;
                    fetchStatus();
                }
            } catch (error) {
                console.error(error);
                alert('Connection error. Failed to toggle service.');
                checkbox.checked = !isChecked;
                fetchStatus();
            }
        }

        // Fetch microservice statuses in real-time
        async function fetchStatus() {
            try {
                const response = await fetch('/services/status');
                const services = await response.json();
                
                services.forEach(service => {
                    const dot = document.getElementById(`status-dot-${service.key}`);
                    const checkbox = document.getElementById(`toggle-${service.key}`);
                    const label = document.getElementById(`toggle-label-${service.key}`);
                    const card = document.getElementById(`service-card-${service.key}`);
                    const portBadge = document.getElementById(`port-badge-${service.key}`);
                    
                    if (dot && checkbox && label) {
                        portBadge.innerText = 'Port ' + service.port;
                        
                        // Don't update checkbox if user is currently toggling it
                        if (!label.classList.contains('animate-pulse')) {
                            if (service.online) {
                                // Online state
                                dot.className = "h-2.5 w-2.5 rounded-full bg-emerald-500 shadow-lg shadow-emerald-500/50 transition-all duration-300";
                                checkbox.checked = true;
                                label.innerText = 'ON';
                                label.className = "text-[9px] font-bold tracking-wide text-emerald-400";
                                card.classList.add('border-emerald-500/20');
                                card.classList.remove('border-white/5');
                            } else {
                                // Offline state
                                dot.className = "h-2.5 w-2.5 rounded-full bg-red-500 shadow-lg shadow-red-500/50 transition-all duration-300";
                                checkbox.checked = false;
                                label.innerText = 'OFF';
                                label.className = "text-[9px] font-bold tracking-wide text-slate-400";
                                card.classList.remove('border-emerald-500/20');
                                card.classList.add('border-white/5');
                            }
                        }
                    }
                });
            } catch (error) {
                console.error('Failed to poll service statuses:', error);
            }
        }

        // Start polling every 3 seconds
        setInterval(fetchStatus, 3000);
        // Initial run
        fetchStatus();
    </script>
</body>
</html>
