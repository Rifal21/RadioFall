<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RadioFall - Comic Stream</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Space Grotesk', sans-serif;
            background-color: #fefce8;
            background-image: radial-gradient(#000 1px, transparent 1px);
            background-size: 20px 20px;
        }

        /* Neobrutalism / Comic Scrollbar */
        ::-webkit-scrollbar {
            width: 12px;
        }

        ::-webkit-scrollbar-track {
            background: #fff;
            border-left: 3px solid #000;
        }

        ::-webkit-scrollbar-thumb {
            background: #fbbf24;
            border: 3px solid #000;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #f59e0b;
        }

        .neo-box {
            background-color: white;
            border: 3px solid black;
            box-shadow: 6px 6px 0px 0px black;
            transition: all 0.2s ease;
        }

        .neo-box:hover {
            box-shadow: 8px 8px 0px 0px black;
            transform: translate(-2px, -2px);
        }

        .neo-btn {
            border: 3px solid black;
            box-shadow: 4px 4px 0px 0px black;
            transition: all 0.1s;
        }

        .neo-btn:active {
            box-shadow: 0px 0px 0px 0px black;
            transform: translate(4px, 4px);
        }

        .neo-tab {
            border: 3px solid black;
            border-bottom: none;
            box-shadow: 4px -2px 0px 0px rgba(0, 0, 0, 0.1);
            transition: all 0.2s;
        }

        .neo-tab.active {
            background-color: #bef264;
            /* lime-300 */
            box-shadow: 4px -4px 0px 0px black;
            transform: translateY(-4px);
            z-index: 10;
        }

        .neo-tab.inactive {
            background-color: #e5e7eb;
            /* gray-200 */
            color: #6b7280;
            cursor: pointer;
        }

        .neo-tab.inactive:hover {
            background-color: #d1d5db;
        }

        .neo-input {
            border: 3px solid black;
            box-shadow: 4px 4px 0px 0px rgba(0, 0, 0, 0.1);
            transition: all 0.2s;
        }

        .neo-input:focus {
            outline: none;
            box-shadow: 4px 4px 0px 0px black;
        }

        .comic-bubble {
            position: relative;
            background: #fff;
            border: 3px solid #000;
        }

        .comic-bubble::after {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            width: 0;
            height: 0;
            border: 10px solid transparent;
            border-right-color: #000;
            border-left: 0;
            margin-top: -10px;
            margin-left: -10px;
        }

        /* Custom Audio Player Styling - Simplified for visibility */
        audio {
            filter: drop-shadow(4px 4px 0px black);
        }

        /* Vinyl Animation */
        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .vinyl-spin {
            animation: spin 2s linear infinite;
        }

        .vinyl-paused {
            animation-play-state: paused !important;
        }

        /* Custom Range Slider */
        input[type=range] {
            -webkit-appearance: none;
            background: transparent;
        }

        /* Thumb Styling */
        input[type=range]::-webkit-slider-thumb {
            -webkit-appearance: none;
            height: 20px;
            width: 20px;
            border-radius: 50%;
            background: #000;
            border: 2px solid #fff;
            cursor: pointer;
            box-shadow: 2px 2px 0px #fbbf24;
            transition: transform 0.1s;
            /* Center thumb on track - margin varies by browser if track height ! thumb height */
            margin-top: -6px;
        }

        input[type=range].vertical-slider::-webkit-slider-thumb {
            margin-top: 0;
            margin-left: -6px;
            /* Adjust for vertical */
        }

        input[type=range]::-webkit-slider-thumb:hover {
            transform: scale(1.2);
        }

        /* Track Styling */
        input[type=range]::-webkit-slider-runnable-track {
            width: 100%;
            height: 8px;
            background: #d1d5db;
            border-radius: 999px;
            border: 2px solid #000;
        }

        input[type=range].vertical-slider {
            -webkit-appearance: slider-vertical;
            width: 8px;
        }

        input[type=range]:focus {
            outline: none;
        }

        /* Digital Marquee Animation */
        .marquee-container {
            overflow: hidden;
            white-space: nowrap;
        }

        .marquee-text {
            display: inline-block;
            padding-left: 100%;
            animation: marquee 15s linear infinite;
        }

        @keyframes marquee {
            0% {
                transform: translate(0, 0);
            }

            100% {
                transform: translate(-100%, 0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.3s ease-out forwards;
        }
    </style>
</head>

<body class="text-black min-h-screen flex flex-col p-4 md:p-8">

    <!-- Navbar -->
    <nav
        class="neo-box bg-cyan-300 rounded-xl px-6 py-4 flex flex-col md:flex-row justify-between items-center mb-8 sticky top-4 z-50 transition-all duration-300">
        <!-- Branding Section - Clickable on Mobile -->
        <div onclick="toggleMobileMenu()"
            class="flex items-center gap-4 mb-0 md:mb-0 transform hover:-rotate-2 transition-transform cursor-pointer md:cursor-default w-full md:w-auto justify-center md:justify-start">
            <div
                class="w-16 h-16 shrink-0 bg-white rounded-xl flex items-center justify-center border-4 border-black shadow-[6px_6px_0_0_#000] overflow-hidden transform group-hover:rotate-6 transition-transform">
                <img src="{{ asset('logo.png') }}" alt="RadioFall Logo" class="w-full h-full object-cover">
            </div>
            <h1 class="text-4xl font-extrabold tracking-tight italic uppercase select-none"
                style="text-shadow: 2px 2px 0px white;">
                Radio<span class="text-pink-500">Fall</span>
            </h1>
            <!-- Mobile indicator -->
            <i id="menu-chevron"
                class="fa-solid fa-chevron-down md:hidden text-black transition-transform duration-300"></i>
        </div>

        <!-- Collapsible Menu -->
        <div id="nav-menu"
            class="w-full md:w-auto overflow-hidden md:overflow-visible max-h-0 md:max-h-none opacity-0 md:opacity-100 transition-all duration-500 flex flex-col md:flex-row items-center gap-4 pt-0 md:pt-0">
            <div class="w-full h-[2px] bg-black/10 md:hidden my-2"></div>
            @guest
                <a href="{{ route('auth.google') }}"
                    class="neo-btn bg-white hover:bg-gray-100 text-black font-black py-2 px-6 rounded-lg text-sm uppercase tracking-wide flex items-center justify-center gap-3 w-full md:w-auto">
                    <i class="fa-brands fa-google text-red-500"></i>
                    Login with Google
                </a>
            @endguest

            @auth
                <div
                    class="flex items-center gap-3 bg-white/50 p-2 rounded-lg border-2 border-black shadow-[2px_2px_0_0_black] w-full md:w-auto justify-center">
                    <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) }}"
                        class="w-8 h-8 rounded-full border-2 border-black" alt="avatar">
                    <span class="font-black text-sm uppercase">{{ auth()->user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-xs font-bold text-red-600 hover:text-red-800 ml-2">
                            <i class="fa-solid fa-right-from-bracket"></i>
                        </button>
                    </form>
                </div>
            @endauth

            <a href="https://saweria.co/Rifal21" target="_blank"
                class="neo-btn bg-lime-400 hover:bg-lime-300 text-black font-black py-3 px-8 rounded-lg text-lg uppercase tracking-wide flex items-center justify-center gap-3 w-full md:w-auto">
                <i class="fa-solid fa-coffee text-xl text-black"></i>
                Buy me a coffee
            </a>

            <button onclick="openRequestModal()"
                class="neo-btn bg-yellow-400 hover:bg-yellow-300 text-black font-black py-3 px-8 rounded-lg text-lg uppercase tracking-wide flex items-center justify-center gap-3 w-full md:w-auto">
                <i class="fa-solid fa-microphone-lines text-xl"></i>
                Request Song!
            </button>
        </div>
    </nav>


    <!-- Main Grid -->
    <main class="flex-grow grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Left Column: Media Tabs -->
        <div class="lg:col-span-2 flex flex-col">

            <!-- Tabs Navigation -->
            <div class="flex gap-4 px-4 translate-y-[3px]">
                <button onclick="switchTab('audio')" id="tab-audio"
                    class="neo-tab active px-6 py-3 rounded-t-xl font-black text-lg uppercase flex items-center gap-2">
                    <i class="fa-solid fa-music"></i> Audio Stream
                </button>
                <button onclick="switchTab('video')" id="tab-video"
                    class="neo-tab inactive px-6 py-3 rounded-t-xl font-black text-lg uppercase flex items-center gap-2">
                    <i class="fa-solid fa-tv"></i> Video Stream
                </button>
            </div>

            <!-- Tab Content Container -->
            <div class="neo-box bg-white p-1 rounded-xl rounded-tl-none z-20 relative">

                <!-- Audio Content -->
                <div id="content-audio" class="p-6 bg-lime-300 rounded-lg flex flex-col gap-6">
                    <!-- Top Info Section -->
                    <div class="flex flex-col md:flex-row items-center gap-6">
                        <div id="vinyl-record"
                            class="w-32 h-32 shrink-0 bg-black rounded-full flex items-center justify-center border-4 border-white shadow-[4px_4px_0_0_rgba(0,0,0,0.5)]">
                            <i class="fa-solid fa-compact-disc text-6xl text-white"></i>
                        </div>
                        <div class="flex-grow text-center md:text-left">
                            <div class="flex flex-wrap justify-center md:justify-start gap-3 mb-2">
                                <div
                                    class="bg-black text-white px-3 py-1 inline-block border-2 border-white transform -rotate-1 shadow-[4px_4px_0_0_#fbbf24]">
                                    <h3 class="text-sm md:text-md font-bold uppercase tracking-widest"><i
                                            class="fa-solid fa-tower-broadcast mr-2 animate-pulse text-red-500"></i>LIVE
                                        ON
                                        AIR
                                    </h3>
                                </div>
                                <div
                                    class="bg-white text-black px-3 py-1 inline-block border-2 border-black transform rotate-1 shadow-[4px_4px_0_0_#06b6d4]">
                                    <h3 class="text-sm md:text-md font-bold uppercase tracking-widest">
                                        <i class="fa-solid fa-headset mr-2 text-blue-500"></i><span
                                            id="listener-count">0</span> LISTENERS
                                    </h3>
                                </div>
                            </div>
                            <p class="text-black font-black text-2xl italic uppercase leading-none">RadioFall Station
                            </p>
                            <p class="text-xs font-bold text-gray-700 tracking-widest mt-1">FM 107.5 &bull; THE COMIC
                                WAVES</p>
                        </div>
                    </div>

                    <!-- DJ Mixer / Deck Section -->
                    <div
                        class="bg-gray-800 p-5 rounded-xl border-4 border-black shadow-[8px_8px_0_0_rgba(0,0,0,0.2)] text-white relative mt-2">
                        <!-- Screws -->
                        <div
                            class="absolute top-3 left-3 w-3 h-3 bg-gray-400 rounded-full border border-black flex items-center justify-center">
                            <div class="w-2 h-0.5 bg-gray-600 transform rotate-45"></div>
                        </div>
                        <div
                            class="absolute top-3 right-3 w-3 h-3 bg-gray-400 rounded-full border border-black flex items-center justify-center">
                            <div class="w-2 h-0.5 bg-gray-600 transform rotate-45"></div>
                        </div>
                        <div
                            class="absolute bottom-3 left-3 w-3 h-3 bg-gray-400 rounded-full border border-black flex items-center justify-center">
                            <div class="w-2 h-0.5 bg-gray-600 transform rotate-45"></div>
                        </div>
                        <div
                            class="absolute bottom-3 right-3 w-3 h-3 bg-gray-400 rounded-full border border-black flex items-center justify-center">
                            <div class="w-2 h-0.5 bg-gray-600 transform rotate-45"></div>
                        </div>

                        <!-- Digital LED Display (Now Playing) -->
                        <div
                            class="bg-black border-4 border-gray-600 rounded-lg p-2 mb-4 mx-4 shadow-[inset_0_2px_4px_rgba(0,0,0,0.8)] relative overflow-hidden h-12 flex items-center">
                            <div
                                class="absolute inset-0 pointer-events-none z-10 opacity-20 bg-[url('https://www.transparenttextures.com/patterns/pixel-weave.png')]">
                            </div>
                            <div class="marquee-container w-full overflow-hidden">
                                <div id="digital-marquee-text"
                                    class="marquee-text font-mono text-green-400 text-lg font-bold tracking-widest uppercase items-center"
                                    style="text-shadow: 0 0 5px #4ade80, 0 0 10px #22c55e;">
                                    <i class="fa-solid fa-music mr-2"></i> WAITING FOR SIGNAL...
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col md:flex-row gap-6 items-end justify-between">

                            <!-- Visualizer Screen -->
                            <div class="w-full md:w-2/3">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1 ml-1">
                                    Frequency Visualizer</p>
                                <div
                                    class="bg-black border-4 border-gray-600 rounded-lg p-3 h-32 flex items-end justify-center gap-1 shadow-[inset_0_2px_4px_rgba(0,0,0,0.6)] relative overflow-hidden">
                                    <!-- Scanlines -->
                                    <div class="absolute inset-0 pointer-events-none z-10"
                                        style="background: linear-gradient(rgba(18,16,16,0) 50%, rgba(0,0,0,0.25) 50%), linear-gradient(90deg, rgba(255,0,0,0.06), rgba(0,255,0,0.02), rgba(0,0,255,0.06)); background-size: 100% 2px, 3px 100%;">
                                    </div>

                                    <!-- Dynamic Bars - Expanded to Fill Width -->
                                    <div
                                        class="viz-bar w-[4%] bg-gradient-to-t from-green-500 via-yellow-400 to-red-500 rounded-t-sm transition-all duration-100 h-[10%]">
                                    </div>
                                    <div
                                        class="viz-bar w-[4%] bg-gradient-to-t from-green-500 via-yellow-400 to-red-500 rounded-t-sm transition-all duration-100 h-[20%]">
                                    </div>
                                    <div
                                        class="viz-bar w-[4%] bg-gradient-to-t from-green-500 via-yellow-400 to-red-500 rounded-t-sm transition-all duration-100 h-[50%]">
                                    </div>
                                    <div
                                        class="viz-bar w-[4%] bg-gradient-to-t from-green-500 via-yellow-400 to-red-500 rounded-t-sm transition-all duration-100 h-[30%]">
                                    </div>
                                    <div
                                        class="viz-bar w-[4%] bg-gradient-to-t from-green-500 via-yellow-400 to-red-500 rounded-t-sm transition-all duration-100 h-[60%]">
                                    </div>
                                    <div
                                        class="viz-bar w-[4%] bg-gradient-to-t from-green-500 via-yellow-400 to-red-500 rounded-t-sm transition-all duration-100 h-[80%]">
                                    </div>
                                    <div
                                        class="viz-bar w-[4%] bg-gradient-to-t from-green-500 via-yellow-400 to-red-500 rounded-t-sm transition-all duration-100 h-[40%]">
                                    </div>
                                    <div
                                        class="viz-bar w-[4%] bg-gradient-to-t from-green-500 via-yellow-400 to-red-500 rounded-t-sm transition-all duration-100 h-[20%]">
                                    </div>
                                    <div
                                        class="viz-bar w-[4%] bg-gradient-to-t from-green-500 via-yellow-400 to-red-500 rounded-t-sm transition-all duration-100 h-[50%]">
                                    </div>
                                    <div
                                        class="viz-bar w-[4%] bg-gradient-to-t from-green-500 via-yellow-400 to-red-500 rounded-t-sm transition-all duration-100 h-[10%]">
                                    </div>
                                    <div
                                        class="viz-bar w-[4%] bg-gradient-to-t from-green-500 via-yellow-400 to-red-500 rounded-t-sm transition-all duration-100 h-[60%]">
                                    </div>
                                    <div
                                        class="viz-bar w-[4%] bg-gradient-to-t from-green-500 via-yellow-400 to-red-500 rounded-t-sm transition-all duration-100 h-[20%]">
                                    </div>
                                    <div
                                        class="viz-bar w-[4%] bg-gradient-to-t from-green-500 via-yellow-400 to-red-500 rounded-t-sm transition-all duration-100 h-[80%]">
                                    </div>
                                    <div
                                        class="viz-bar w-[4%] bg-gradient-to-t from-green-500 via-yellow-400 to-red-500 rounded-t-sm transition-all duration-100 h-[50%]">
                                    </div>
                                    <div
                                        class="viz-bar w-[4%] bg-gradient-to-t from-green-500 via-yellow-400 to-red-500 rounded-t-sm transition-all duration-100 h-[10%]">
                                    </div>
                                    <div
                                        class="viz-bar w-[4%] bg-gradient-to-t from-green-500 via-yellow-400 to-red-500 rounded-t-sm transition-all duration-100 h-[30%]">
                                    </div>
                                    <div
                                        class="viz-bar w-[4%] bg-gradient-to-t from-green-500 via-yellow-400 to-red-500 rounded-t-sm transition-all duration-100 h-[70%]">
                                    </div>
                                    <div
                                        class="viz-bar w-[4%] bg-gradient-to-t from-green-500 via-yellow-400 to-red-500 rounded-t-sm transition-all duration-100 h-[20%]">
                                    </div>
                                    <div
                                        class="viz-bar w-[4%] bg-gradient-to-t from-green-500 via-yellow-400 to-red-500 rounded-t-sm transition-all duration-100 h-[90%]">
                                    </div>
                                    <div
                                        class="viz-bar w-[4%] bg-gradient-to-t from-green-500 via-yellow-400 to-red-500 rounded-t-sm transition-all duration-100 h-[40%]">
                                    </div>
                                    <div
                                        class="viz-bar w-[4%] bg-gradient-to-t from-green-500 via-yellow-400 to-red-500 rounded-t-sm transition-all duration-100 h-[10%]">
                                    </div>
                                    <div
                                        class="viz-bar w-[4%] bg-gradient-to-t from-green-500 via-yellow-400 to-red-500 rounded-t-sm transition-all duration-100 h-[60%]">
                                    </div>
                                    <div
                                        class="viz-bar w-[4%] bg-gradient-to-t from-green-500 via-yellow-400 to-red-500 rounded-t-sm transition-all duration-100 h-[20%]">
                                    </div>
                                    <div
                                        class="viz-bar w-[4%] bg-gradient-to-t from-green-500 via-yellow-400 to-red-500 rounded-t-sm transition-all duration-100 h-[50%]">
                                    </div>
                                </div>
                            </div>

                            <!-- Controls -->
                            <div class="w-full md:w-1/3 flex justify-around items-end">
                                <!-- Play Button -->
                                <div class="flex flex-col items-center gap-2">
                                    <button id="custom-play-btn"
                                        class="w-16 h-16 bg-yellow-400 hover:bg-yellow-300 border-[3px] border-black rounded-full flex items-center justify-center text-3xl transition active:scale-95 shadow-[0_4px_0_0_#b45309] active:shadow-none active:translate-y-1">
                                        <i id="play-icon" class="fa-solid fa-play ml-1 text-black"></i>
                                    </button>
                                    <span class="text-[10px] font-bold text-gray-400 uppercase">Start / Stop</span>
                                </div>

                                <!-- Volume Fader (Vertical) -->
                                <div class="flex flex-col items-center gap-2 relative">
                                    <div
                                        class="h-32 bg-black border-2 border-gray-600 rounded-full p-1 relative flex justify-center shadow-inner">
                                        <!-- Track Lines -->
                                        <div
                                            class="absolute inset-y-2 left-1/2 w-0.5 bg-gray-700 -translate-x-1/2 pointer-events-none">
                                        </div>
                                        <!-- Vertical Slider Input -->
                                        <input type="range" id="volume-slider" min="0" max="1"
                                            step="0.01" value="1"
                                            class="h-full w-8 appearance-none bg-transparent cursor-pointer vertical-slider z-20"
                                            style="writing-mode: bt-lr; -webkit-appearance: slider-vertical;">
                                    </div>
                                    <button id="mute-btn"
                                        class="text-xs font-bold text-gray-400 uppercase hover:text-white flex items-center gap-1">
                                        <i id="volume-icon" class="fa-solid fa-volume-high"></i> Master
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Hidden Audio -->
                    <audio id="audio-player" class="hidden"
                        src="https://radio.fkstudio.my.id/listen/radio_fkstudio/radio.mp3"></audio>
                </div>

                <!-- Video Content (Hidden by default) -->
                <div id="content-video" class="hidden">
                    <div
                        class="bg-pink-100 rounded-lg overflow-hidden relative flex items-center justify-center aspect-video group border-3 border-black">
                        <div
                            class="absolute w-full h-full bg-[url('https://www.transparenttextures.com/patterns/diag-stripes-light.png')] opacity-30">
                        </div>

                        <div class="z-10 text-center transform group-hover:scale-110 transition duration-500">
                            <div
                                class="bg-white border-4 border-black p-6 rotate-3 shadow-[8px_8px_0_0_rgba(0,0,0,1)] inline-block">
                                <h2 class="text-5xl font-black uppercase text-black mb-2"><i
                                        class="fa-solid fa-video mr-3"></i>Video Stream</h2>
                                <span
                                    class="bg-black text-white text-xl font-bold px-4 py-1 rotate-[-2deg] inline-block">COMING
                                    SOON</span>
                            </div>
                        </div>

                        <!-- Decorative Elements -->
                        <div class="absolute top-4 right-4 text-6xl animate-bounce"><i
                                class="fa-solid fa-clapperboard"></i></div>
                        <div class="absolute bottom-4 left-4 text-6xl animate-pulse"><i class="fa-solid fa-star"></i>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Recent Requests Section -->
            <div class="mt-8 neo-box bg-white p-6 rounded-xl border-4 border-black shadow-[8px_8px_0_0_rgba(0,0,0,1)]">
                <div class="flex justify-between items-center mb-4 border-b-4 border-black pb-2">
                    <h3 class="font-black text-2xl uppercase flex items-center gap-2">
                        <i class="fa-solid fa-list-ul text-pink-500"></i> Recent Requests
                    </h3>
                    <div class="bg-black text-white text-xs font-bold px-2 py-1 transform rotate-2">
                        LATEST 5
                    </div>
                </div>

                <div id="requests-container" class="space-y-3">
                    @if (isset($recentRequests))
                        @forelse($recentRequests as $req)
                            <div
                                class="flex items-center justify-between p-3 bg-yellow-50 border-2 border-black rounded-lg shadow-[2px_2px_0_0_rgba(0,0,0,0.1)] hover:scale-[1.01] transition-transform">
                                <div class="overflow-hidden mr-2">
                                    <p class="font-black text-black uppercase text-sm truncate"><i
                                            class="fa-solid fa-music mr-2 text-gray-400"></i>{{ $req->song_title }}</p>
                                    <p class="text-xs text-gray-600 font-bold italic pl-6 truncate">
                                        {{ $req->song_artist }}</p>
                                </div>
                                <div class="text-right shrink-0">
                                    <span
                                        class="bg-cyan-300 text-black text-[10px] font-black px-2 py-1 border border-black rounded transform -rotate-2 inline-block shadow-[2px_2px_0_0_black]">
                                        {{ $req->requester_name }}
                                    </span>
                                    <p class="text-[10px] font-bold text-gray-400 mt-1"><i
                                            class="fa-regular fa-clock mr-1"></i>{{ $req->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 border-2 border-dashed border-gray-400 rounded-lg">
                                <p class="font-bold text-gray-500 italic text-lg">NO REQUESTS YET</p>
                                <p class="text-xs font-bold text-gray-400 mt-1">BE THE FIRST TO REQUEST A SONG! 🎧</p>
                            </div>
                        @endforelse
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column: Chat -->
        <div
            class="neo-box bg-white rounded-xl flex flex-col h-[600px] lg:h-auto overflow-hidden relative border-4 border-black shadow-[8px_8px_0_0_rgba(0,0,0,1)]">
            <div class="bg-blue-500 p-4 border-b-4 border-black flex justify-between items-center">
                <h3 class="font-black text-2xl text-white uppercase italic tracking-wider flex items-center gap-2"
                    style="-webkit-text-stroke: 1.5px black;">
                    <i class="fa-solid fa-comments"></i> Live Chat
                </h3>
                <div class="w-4 h-4 bg-green-400 rounded-full border-2 border-black animate-ping"></div>
            </div>

            <div id="chat-container"
                class="flex-grow p-4 overflow-y-auto space-y-4 bg-[url('https://www.transparenttextures.com/patterns/graphy.png')] bg-gray-50">
                @foreach ($messages as $msg)
                    <div class="flex flex-col group">
                        <div class="flex items-center gap-2 mb-1 ml-2 self-start">
                            <img src="{{ $msg->sender_avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($msg->sender_name) }}"
                                class="w-5 h-5 rounded-full border-1 border-black shadow-[1px_1px_0_0_black]"
                                alt="avatar">
                            <span
                                class="text-xs font-black text-black bg-yellow-300 inline-block px-2 border-2 border-black -rotate-1 shadow-[2px_2px_0_0_black]">{{ $msg->sender_name }}</span>
                        </div>
                        <div
                            class="bg-white border-3 border-black p-3 rounded-2xl rounded-tl-none shadow-[4px_4px_0_0_rgba(0,0,0,0.1)] group-hover:shadow-[4px_4px_0_0_rgba(0,0,0,1)] transition-all ml-1">
                            <p class="text-black font-medium leading-tight">{{ $msg->message }}</p>
                        </div>
                        <span
                            class="text-xs text-gray-500 font-bold mt-1 text-right mr-2">{{ $msg->created_at->format('H:i') }}</span>
                    </div>
                @endforeach
            </div>

            <script>
                window.isAuthenticated = @json(auth()->check());
                window.userName = @json(auth()->user()->name ?? null);
                window.userAvatar = @json(auth()->user()->avatar ?? null);
            </script>

            <div class="p-4 bg-yellow-100 border-t-4 border-black">
                @auth
                    <form id="chat-form" onsubmit="event.preventDefault(); sendMessage();" class="flex flex-col gap-3">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xs font-black bg-black text-white px-2 py-0.5 rounded">CHATTING
                                AS:</span>
                            <span class="text-xs font-black uppercase text-blue-600">{{ auth()->user()->name }}</span>
                        </div>
                        <div class="flex gap-2">
                            <input type="text" id="chat-message" name="message" placeholder="SAY SOMETHING COOL..."
                                class="neo-input flex-grow p-3 rounded-lg font-bold text-sm" required>
                            <button type="submit"
                                class="neo-btn bg-cyan-400 hover:bg-cyan-300 text-black p-3 rounded-lg flex items-center justify-center w-12">
                                <i class="fa-solid fa-paper-plane text-lg"></i>
                            </button>
                        </div>
                    </form>
                @else
                    <div class="text-center py-4 flex flex-col gap-3">
                        <p class="font-black text-sm uppercase tracking-tight">Login to join the conversation!</p>
                        <a href="{{ route('auth.google') }}"
                            class="neo-btn bg-white hover:bg-gray-100 text-black font-black py-3 px-6 rounded-lg text-sm uppercase tracking-wide flex items-center justify-center gap-3 mx-auto">
                            <i class="fa-brands fa-google text-red-500"></i>
                            Login with Google
                        </a>
                    </div>
                @endauth
            </div>
    </main>

    <!-- Footer -->
    <footer class="mt-8 text-center font-bold text-gray-400 uppercase tracking-widest text-xs">
        &copy; 2026 RadioFall <i class="fa-solid fa-bolt mx-1"></i> Stay Tuned <i class="fa-solid fa-bolt mx-1"></i>
        Stay Awesome
    </footer>

    <script>
        // Mobile Menu Toggle
        function toggleMobileMenu() {
            if (window.innerWidth >= 768) return; // Prevent on desktop

            const menu = document.getElementById('nav-menu');
            const chevron = document.getElementById('menu-chevron');
            const isExpanded = menu.style.maxHeight !== '0px' && menu.style.maxHeight !== '';

            if (isExpanded) {
                menu.style.maxHeight = '0px';
                menu.style.opacity = '0';
                menu.style.paddingTop = '0px';
                chevron.style.transform = 'rotate(0deg)';
            } else {
                menu.style.maxHeight = '500px'; // Give enough space
                menu.style.opacity = '1';
                menu.style.paddingTop = '1rem';
                chevron.style.transform = 'rotate(180deg)';
            }
        }

        // Tab Switching Logic
        function switchTab(tab) {
            const tabs = ['audio', 'video'];
            tabs.forEach(t => {
                const btn = document.getElementById(`tab-${t}`);
                const content = document.getElementById(`content-${t}`);

                if (t === tab) {
                    btn.classList.add('active');
                    btn.classList.remove('inactive');
                    content.classList.remove('hidden');
                } else {
                    btn.classList.add('inactive');
                    btn.classList.remove('active');
                    content.classList.add('hidden');
                }
            });
        }

        const songs = @json($songs);
        let chatContainer = document.getElementById('chat-container');
        // Scroll to bottom on load
        if (chatContainer) chatContainer.scrollTop = chatContainer.scrollHeight;

        function openRequestModal() {
            if (!window.isAuthenticated) {
                Swal.fire({
                    title: '<span class="text-2xl font-black uppercase">Authentication Required</span>',
                    html: `
                        <div class="py-4">
                            <p class="font-bold text-gray-600 mb-6 uppercase text-sm">You must be logged in with Google to request a song!</p>
                            <a href="{{ route('auth.google') }}" class="neo-btn bg-white hover:bg-gray-100 text-black font-black py-3 px-8 rounded-lg text-sm uppercase tracking-wide flex items-center justify-center gap-3">
                                <i class="fa-brands fa-google text-red-500 text-xl"></i>
                                Login with Google
                            </a>
                        </div>
                    `,
                    showConfirmButton: false,
                    showCloseButton: true,
                    background: '#fff',
                    color: '#000',
                    customClass: {
                        popup: 'border-4 border-black shadow-[10px_10px_0_0_rgba(0,0,0,1)] rounded-xl'
                    }
                });
                return;
            }

            let htmlContent = '<div class="space-y-3 max-h-60 overflow-y-auto custom-scrollbar pr-2 mt-4">';
            if (songs.length === 0) {
                htmlContent +=
                    '<p class="text-center font-bold text-gray-500 py-4"><i class="fa-regular fa-face-sad-tear mr-2"></i>NO SONGS AVAILABLE</p>';
            }
            songs.forEach(song => {
                const safeTitle = song.title.replace(/'/g, "\\'");
                const safeArtist = (song.artist || '').replace(/'/g, "\\'");
                const safeRequestUrl = (song.request_url || '').replace(/'/g, "\\'");
                const safeArtistDisplay = song.artist || 'Unknown Artist';

                htmlContent += `
                    <div class="flex justify-between items-center p-3 bg-white border-2 border-black shadow-[4px_4px_0_0_rgba(0,0,0,0.1)] hover:shadow-[4px_4px_0_0_#fbbf24] hover:-translate-y-1 transition-all rounded-lg group mb-2">
                        <div class="overflow-hidden mr-3 text-left">
                            <p class="font-black text-black truncate uppercase text-sm"><i class="fa-solid fa-music mr-2 text-gray-400"></i>${song.title}</p>
                            <p class="text-xs font-bold text-gray-500 truncate italic pl-6">${safeArtistDisplay}</p>
                        </div>
                        <button onclick="submitRequest('${safeTitle}', '${safeArtist}', '${safeRequestUrl}')" class="bg-black hover:bg-gray-800 text-white font-bold text-xs px-4 py-2 rounded-md shadow-[2px_2px_0_0_rgba(0,0,0,0.2)]">
                            PICK <i class="fa-solid fa-check ml-1"></i>
                        </button>
                    </div>
                `;
            });
            htmlContent += '</div>';

            Swal.fire({
                title: '<span class="text-3xl font-black uppercase italic border-b-4 border-yellow-400 inline-block px-4">Request A Track</span>',
                html: htmlContent,
                showCloseButton: true,
                showConfirmButton: false,
                background: '#fff',
                color: '#000',
                width: '550px',
                padding: '2em',
                customClass: {
                    popup: 'border-4 border-black shadow-[10px_10px_0_0_rgba(0,0,0,1)] rounded-xl'
                }
            });
        }

        function submitRequest(songTitle, songArtist, requestUrl) {
            Swal.close();

            if (!window.isAuthenticated) {
                window.location.href = "{{ route('auth.google') }}";
                return;
            }

            setTimeout(() => {
                Swal.fire({
                    title: `<span class="uppercase font-black text-xl">Request "${songTitle}"?</span>`,
                    text: `Requesting as ${window.userName}`,
                    showCancelButton: true,
                    confirmButtonText: 'SEND IT! 🚀',
                    cancelButtonText: 'NAH',
                    confirmButtonColor: '#000',
                    cancelButtonColor: '#ef4444',
                    background: '#fff',
                    color: '#000',
                    customClass: {
                        popup: 'border-4 border-black shadow-[10px_10px_0_0_rgba(0,0,0,1)] rounded-xl font-bold',
                        confirmButton: 'neo-btn',
                        cancelButton: 'neo-btn'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('/request', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                        .getAttribute('content')
                                },
                                body: JSON.stringify({
                                    song_title: songTitle,
                                    song_artist: songArtist,
                                    request_url: requestUrl
                                })
                            })
                            .then(response => {
                                if (response.status === 401) {
                                    window.location.href = "{{ route('auth.google') }}";
                                    return;
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (data && data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'BOOM! SENT!',
                                        text: data.message,
                                        timer: 3000,
                                        showConfirmButton: false,
                                        background: '#fff',
                                        color: '#000',
                                        customClass: {
                                            popup: 'border-4 border-black shadow-[10px_10px_0_0_#22c55e] rounded-xl'
                                        }
                                    });
                                    fetchRequests();
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'OWH SNAP!',
                                        text: data.message || 'Server rejected your request.',
                                        background: '#fff',
                                        color: '#000',
                                        customClass: {
                                            popup: 'border-4 border-black shadow-[10px_10px_0_0_#ef4444] rounded-xl'
                                        }
                                    });
                                }
                            })
                            .catch(error => {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'OOPS!',
                                    text: 'Something broke. Try again later!',
                                    background: '#fff',
                                    color: '#000',
                                    customClass: {
                                        popup: 'border-4 border-black shadow-[10px_10px_0_0_#ef4444] rounded-xl'
                                    }
                                });
                            });
                    }
                });
            }, 300);
        }

        function sendMessage() {
            if (!window.isAuthenticated) {
                window.location.href = "{{ route('auth.google') }}";
                return;
            }

            const message = document.getElementById('chat-message').value;
            const name = window.userName;
            const avatar = window.userAvatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}`;

            if (!message) return;

            // Optimistic Update
            const tempTime = new Date().toLocaleTimeString([], {
                hour: '2-digit',
                minute: '2-digit'
            });
            const tempHtml = `
                 <div class="flex flex-col group opacity-50" id="temp-msg">
                    <div class="flex items-center gap-2 mb-1 ml-2 self-start">
                        <img src="${avatar}" class="w-5 h-5 rounded-full border-1 border-black shadow-[1px_1px_0_0_black]" alt="avatar">
                        <span class="text-xs font-black text-black bg-gray-200 inline-block px-2 border-2 border-black -rotate-1 self-start">${name}</span>
                    </div>
                    <div class="bg-white border-3 border-black p-3 rounded-2xl rounded-tl-none shadow-[4px_4px_0_0_rgba(0,0,0,0.1)] ml-1">
                        <p class="text-black font-medium leading-tight">${message}</p>
                    </div>
                    <span class="text-xs text-gray-500 font-bold mt-1 text-right mr-2">${tempTime}</span>
                </div>
            `;
            chatContainer.insertAdjacentHTML('beforeend', tempHtml);
            chatContainer.scrollTop = chatContainer.scrollHeight;

            document.getElementById('chat-message').value = '';

            fetch('/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        message: message
                    })
                })
                .then(res => {
                    if (res.status === 401) {
                        window.location.href = "{{ route('auth.google') }}";
                        return;
                    }
                    return res.json();
                })
                .then(data => {
                    if (data && data.success) {
                        const temp = document.getElementById('temp-msg');
                        if (temp) temp.remove();
                        fetchMessages();
                    }
                });
        }

        function fetchMessages() {
            fetch('/chat/messages')
                .then(res => res.json())
                .then(data => {
                    const wasAtBottom = chatContainer.scrollHeight - chatContainer.scrollTop <= chatContainer
                        .clientHeight + 100;

                    let html = '';
                    data.forEach(msg => {
                        const time = new Date(msg.created_at).toLocaleTimeString([], {
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                        const avatar = msg.sender_avatar ||
                            `https://ui-avatars.com/api/?name=${encodeURIComponent(msg.sender_name)}`;

                        html += `
                        <div class="flex flex-col group animate-fade-in">
                            <div class="flex items-center gap-2 mb-1 ml-2 self-start">
                                <img src="${avatar}" class="w-5 h-5 rounded-full border-1 border-black shadow-[1px_1px_0_0_black]" alt="avatar">
                                <span class="text-xs font-black text-black bg-yellow-300 inline-block px-2 border-2 border-black -rotate-1 shadow-[2px_2px_0_0_black]">${msg.sender_name}</span>
                            </div>
                            <div class="bg-white border-3 border-black p-3 rounded-2xl rounded-tl-none shadow-[4px_4px_0_0_rgba(0,0,0,0.1)] group-hover:shadow-[4px_4px_0_0_rgba(0,0,0,1)] transition-all ml-1">
                                <p class="text-black font-medium leading-tight">${msg.message}</p>
                            </div>
                            <span class="text-xs text-gray-500 font-bold mt-1 text-right mr-2">${time}</span>
                        </div>
                    `;
                    });

                    if (chatContainer.innerHTML !== html) {
                        chatContainer.innerHTML = html;
                        if (wasAtBottom) {
                            chatContainer.scrollTop = chatContainer.scrollHeight;
                        }
                    }
                });
        }

        setInterval(fetchMessages, 3000);

        // Vinyl Spin Logic & Audio Controls
        const audioPlayer = document.getElementById('audio-player');
        const vinylRecord = document.getElementById('vinyl-record');
        const playBtn = document.getElementById('custom-play-btn');
        const playIcon = document.getElementById('play-icon');
        const volumeSlider = document.getElementById('volume-slider');
        const muteBtn = document.getElementById('mute-btn');
        const volumeIcon = document.getElementById('volume-icon');

        if (audioPlayer) {
            // Play/Pause Toggle
            playBtn.addEventListener('click', () => {
                if (audioPlayer.paused) {
                    audioPlayer.play();
                } else {
                    audioPlayer.pause();
                }
            });

            // Update UI on Play
            audioPlayer.addEventListener('play', () => {
                if (vinylRecord) {
                    vinylRecord.classList.add('vinyl-spin');
                    vinylRecord.classList.remove('vinyl-paused');
                }
                playIcon.classList.remove('fa-play');
                playIcon.classList.add('fa-pause');
                playBtn.classList.remove('bg-yellow-400', 'hover:bg-yellow-300');
                playBtn.classList.add('bg-green-400', 'hover:bg-green-300');
            });

            // Update UI on Pause
            audioPlayer.addEventListener('pause', () => {
                if (vinylRecord) {
                    vinylRecord.classList.add('vinyl-paused');
                }
                playIcon.classList.remove('fa-pause');
                playIcon.classList.add('fa-play');
                playBtn.classList.add('bg-yellow-400', 'hover:bg-yellow-300');
                playBtn.classList.remove('bg-green-400', 'hover:bg-green-300');
            });

            // Volume Slider Control
            volumeSlider.addEventListener('input', (e) => {
                const value = e.target.value;
                audioPlayer.volume = value;
                updateVolumeIcon(value);
            });

            // Mute Button Control
            muteBtn.addEventListener('click', () => {
                if (audioPlayer.muted) {
                    audioPlayer.muted = false;
                    volumeSlider.value = audioPlayer.volume;
                    updateVolumeIcon(audioPlayer.volume);
                } else {
                    audioPlayer.muted = true;
                    volumeSlider.value = 0;
                    updateVolumeIcon(0);
                }
            });

            function updateVolumeIcon(vol) {
                volumeIcon.className = ''; // Clear classes
                if (vol == 0) {
                    volumeIcon.className = 'fa-solid fa-volume-xmark text-xl';
                } else if (vol < 0.5) {
                    volumeIcon.className = 'fa-solid fa-volume-low text-xl';
                } else {
                    volumeIcon.className = 'fa-solid fa-volume-high text-xl';
                }
            }

            // Fake Visualizer Loop
            function animateVisualizer() {
                if (!audioPlayer.paused) {
                    const bars = document.querySelectorAll('.viz-bar');
                    if (bars.length > 0) {
                        bars.forEach(bar => {
                            // Random height between 10% and 100%
                            const height = Math.floor(Math.random() * 90) + 10;
                            bar.style.height = `${height}%`;
                        });
                    }
                }
                setTimeout(animateVisualizer, 100); // Update every 100ms for retro feel
            }
            // Start the loop
            animateVisualizer();

            // Fetch Now Playing Data
            function fetchNowPlaying() {
                // Using AzuraCast public API for now playing (assuming standard endpoint based on radio URL provided)
                fetch('https://radio.fkstudio.my.id/api/nowplaying/radio_fkstudio')
                    .then(response => response.json())
                    .then(data => {
                        console.log(data);
                        const marquee = document.getElementById('digital-marquee-text');
                        const listenerEl = document.getElementById('listener-count');

                        // Update Listeners
                        if (data && data.listeners) {
                            listenerEl.innerText = data.listeners.total || 0;
                        }

                        if (data && data.now_playing && data.now_playing.song) {
                            const song = data.now_playing.song;
                            const text = `${song.artist} - ${song.title}`;
                            // Update only if text changed to avoid jumpiness, or force update if placeholder
                            if (!marquee.innerText.includes(text)) {
                                marquee.innerHTML =
                                    `<i class="fa-solid fa-music mr-3"></i> ${text} <span class="mx-8">***</span> <i class="fa-solid fa-compact-disc mr-3"></i> ${text} <span class="mx-8">***</span>`;
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching song data:', error);
                    });
            }

            // Fetch immediately and then every 15 seconds
            fetchNowPlaying();
            setInterval(fetchNowPlaying, 15000);

            // Fetch Recent Requests
            function fetchRequests() {
                fetch('/request/list')
                    .then(response => response.json())
                    .then(data => {
                        const container = document.getElementById('requests-container');
                        if (!container) return;

                        let html = '';
                        if (data.length === 0) {
                            html = `
                                <div class="text-center py-8 border-2 border-dashed border-gray-400 rounded-lg">
                                    <p class="font-bold text-gray-500 italic text-lg">NO REQUESTS YET</p>
                                    <p class="text-xs font-bold text-gray-400 mt-1">BE THE FIRST TO REQUEST A SONG! 🎧</p>
                                </div>`;
                        } else {
                            data.forEach(req => {
                                const date = new Date(req.created_at);
                                const timeStr = date.toLocaleTimeString([], {
                                    hour: '2-digit',
                                    minute: '2-digit'
                                });

                                html += `
                                <div class="flex items-center justify-between p-3 bg-yellow-50 border-2 border-black rounded-lg shadow-[2px_2px_0_0_rgba(0,0,0,0.1)] hover:scale-[1.01] transition-transform animate-fade-in">
                                    <div class="overflow-hidden mr-2">
                                        <p class="font-black text-black uppercase text-sm truncate"><i class="fa-solid fa-music mr-2 text-gray-400"></i>${req.song_title}</p>
                                        <p class="text-xs text-gray-600 font-bold italic pl-6 truncate">${req.song_artist || ''}</p>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <span class="bg-cyan-300 text-black text-[10px] font-black px-2 py-1 border border-black rounded transform -rotate-2 inline-block shadow-[2px_2px_0_0_black]">
                                            ${req.requester_name}
                                        </span>
                                        <p class="text-[10px] font-bold text-gray-400 mt-1"><i class="fa-regular fa-clock mr-1"></i>${timeStr}</p>
                                    </div>
                                </div>`;
                            });
                        }
                        // Only update if content changed to avoid repaint if identical (optional optimization, but innerHTML is cheap enough for 5 items)
                        if (container.innerHTML !== html) {
                            container.innerHTML = html;
                        }
                    });
            }
            setInterval(fetchRequests, 5000); // Check for requests every 5s
        }
    </script>
</body>

</html>
