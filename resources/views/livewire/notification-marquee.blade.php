<div wire:poll.10s>
    @if($notifications->count() > 0)
        @php $notification = $notifications->first(); @endphp
        <div x-cloak
             wire:key="{{ $notification->id }}"
             x-data="{ visible: false, latestId: {{ $notification->id }} }"
             x-init="
                const seenId = parseInt(localStorage.getItem('marquee_seen_id') || 0);
                if (latestId > seenId) {
                    visible = true;
                    localStorage.setItem('marquee_seen_id', latestId);
                }
             "
             x-show="visible"
             x-transition:enter="transition ease-out duration-500"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
            class="fixed top-[52px] left-0 w-full z-[19] overflow-hidden bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 shadow-md">
            <div class="marquee-container py-1.5">
                <div class="marquee-content flex items-center gap-12 whitespace-nowrap text-white text-sm font-medium"
                     x-init="$el.addEventListener('animationend', () => { visible = false })">
                    <span class="inline-flex items-center gap-2">
                        <svg class="w-4 h-4 text-yellow-300 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                        </svg>
                        <span>{{ $notification->message }}</span>
                        <span class="text-white/50 text-xs">&bull; {{ $notification->created_at->diffForHumans() }}</span>
                    </span>
                    {{-- Duplicate for seamless scroll --}}
                    <span class="inline-flex items-center gap-2">
                        <svg class="w-4 h-4 text-yellow-300 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                        </svg>
                        <span>{{ $notification->message }}</span>
                        <span class="text-white/50 text-xs">&bull; {{ $notification->created_at->diffForHumans() }}</span>
                    </span>
                </div>
            </div>
        </div>

        <style>
            [x-cloak] { display: none !important; }

            .marquee-container {
                overflow: hidden;
                width: 100%;
            }

            .marquee-content {
                display: inline-flex;
                animation: marquee-scroll 20s linear 2 forwards;
            }

            .marquee-content:hover {
                animation-play-state: paused;
            }

            @keyframes marquee-scroll {
                0% {
                    transform: translateX(0%);
                }
                100% {
                    transform: translateX(-50%);
                }
            }
        </style>
    @endif
</div>
