@extends('layouts.app')

@section('title', 'Asisten Gizi AI — NutriScan AI')
@section('page_title', 'AI Nutrition Assistant')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Top Medical & Educational Disclaimer -->
    <div class="p-4 rounded-2xl bg-cream-100 border border-stone-200/90 shadow-sm flex items-start gap-3.5">
        <div class="p-2 rounded-xl bg-emerald-100 text-emerald-800 flex-shrink-0">
            <i data-lucide="bot" class="w-5 h-5"></i>
        </div>
        <div class="space-y-1 text-xs text-charcoal-muted">
            <div class="flex items-center gap-2">
                <strong class="text-charcoal">Asisten Nutrisi Edukatif & Berbasis Sains</strong>
                @if(!$isAiConfigured)
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-900">[MODE DEMO]</span>
                @else
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-900">[9ROUTER ACTIVE]</span>
                @endif
            </div>
            <p class="leading-relaxed">
                Asisten ini memberikan saran seputar gizi seimbang, estimasi kalori hidangan, dan ide variasi menu sehat. <em>Peringatan: Bukan pengganti konsultasi medis profesional. Asisten tidak memberikan diagnosis penyakit atau resep terapi klinis.</em>
            </p>
        </div>
    </div>

    <!-- Chat Card Container -->
    <div class="bg-cream-100 rounded-3xl border border-stone-200/90 shadow-sm flex flex-col h-[650px] overflow-hidden">

        <!-- Chat Header -->
        <div class="px-6 py-4 bg-cream-200/70 border-b border-stone-200/80 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="relative">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-800 text-white flex items-center justify-center font-bold text-sm shadow-sm">
                        <i data-lucide="sparkles" class="w-5 h-5 text-emerald-200"></i>
                    </div>
                    <span class="absolute bottom-0 right-0 w-3 h-3 rounded-full bg-emerald-500 border-2 border-white"></span>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-emerald-950">NutriScan Assistant</h3>
                    <p class="text-[11px] text-charcoal-muted">Kontekstual: Target {{ $goal->calorie_goal ?: 2000 }} kkal &bull; {{ $goal->protein_goal ?: 60 }}g Protein</p>
                </div>
            </div>

            <!-- Clear Chat Button -->
            <button onclick="clearChatHistory()" class="text-xs text-charcoal-muted hover:text-red-700 font-medium flex items-center gap-1.5 transition">
                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                <span class="hidden sm:inline">Mulai Ulang Percakapan</span>
            </button>
        </div>

        <!-- Chat Messages Area -->
        <div id="chatMessages" class="flex-1 p-6 overflow-y-auto space-y-4 custom-scrollbar">

            <!-- Initial Assistant Greeting -->
            <div class="flex items-start gap-3 max-w-[85%]">
                <div class="w-8 h-8 rounded-xl bg-emerald-800 text-white flex items-center justify-center flex-shrink-0 text-xs shadow-sm">
                    <i data-lucide="bot" class="w-4 h-4 text-emerald-200"></i>
                </div>
                <div class="p-4 rounded-2xl rounded-tl-sm bg-cream-200 text-charcoal text-xs sm:text-sm leading-relaxed border border-stone-200/80 shadow-sm space-y-2">
                    <p>
                        Halo <strong>{{ $user->name }}</strong>! Saya asisten gizi pribadi Anda di NutriScan AI.
                    </p>
                    <p>
                        Saya dapat membantu menjelaskan kandungan nutrisi masakan Indonesia, memberikan tips agar target kalori dan protein harian tercapai, atau merekomendasikan alternatif menu sehat. Ada yang ingin Anda tanyakan hari ini?
                    </p>
                </div>
            </div>

        </div>

        <!-- Quick Question Chips -->
        <div class="px-6 py-2 bg-cream-100 border-t border-stone-200/60 flex items-center gap-2 overflow-x-auto custom-scrollbar text-[11px]">
            <span class="text-charcoal-light flex-shrink-0 font-semibold">Saran:</span>
            <button onclick="sendQuickPrompt('Berapa estimasi kalori dan makronutrien sepiring nasi goreng telur?')" class="flex-shrink-0 px-3 py-1 rounded-full bg-cream-200 hover:bg-emerald-100 hover:text-emerald-900 border border-stone-300/70 transition">
                Kalori Nasi Goreng Telur?
            </button>
            <button onclick="sendQuickPrompt('Bagaimana cara mudah mencukupi target protein 70g sehari dengan lauk lokal?')" class="flex-shrink-0 px-3 py-1 rounded-full bg-cream-200 hover:bg-emerald-100 hover:text-emerald-900 border border-stone-300/70 transition">
                Tips Capai Protein 70g
            </button>
            <button onclick="sendQuickPrompt('Rekomendasi camilan sehat rendah lemak dan gula di sela jam kuliah.')" class="flex-shrink-0 px-3 py-1 rounded-full bg-cream-200 hover:bg-emerald-100 hover:text-emerald-900 border border-stone-300/70 transition">
                Ide Camilan Sehat
            </button>
        </div>

        <!-- Chat Input Form -->
        <div class="p-4 bg-cream-200/70 border-t border-stone-200/80">
            <form id="chatForm" onsubmit="handleChatSubmit(event)" class="flex items-center gap-2">
                <input type="text" id="chatInput" placeholder="Tanyakan seputar gizi makanan Anda..." autocomplete="off"
                       class="flex-1 px-4 py-3 rounded-2xl border border-stone-300 bg-cream-50 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-emerald-700 transition">

                <button type="submit" id="sendBtn" class="px-5 py-3 rounded-2xl bg-emerald-800 text-white font-bold text-xs sm:text-sm hover:bg-emerald-900 shadow-sm transition flex items-center gap-2 flex-shrink-0">
                    <span>Kirim</span>
                    <i data-lucide="send" class="w-4 h-4"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    let conversationHistory = [];

    function appendMessage(role, content) {
        const container = document.getElementById('chatMessages');

        const messageWrapper = document.createElement('div');
        messageWrapper.className = role === 'user'
            ? 'flex items-start justify-end gap-3 max-w-[85%] ml-auto'
            : 'flex items-start gap-3 max-w-[85%]';

        if (role === 'user') {
            messageWrapper.innerHTML = `
                <div class="p-4 rounded-2xl rounded-tr-sm bg-emerald-800 text-white text-xs sm:text-sm leading-relaxed shadow-sm">
                    ${escapeHtml(content)}
                </div>
                <div class="w-8 h-8 rounded-xl bg-emerald-950 text-white flex items-center justify-center flex-shrink-0 font-bold text-xs">
                    ${escapeHtml('{{ substr(Auth::user()->name, 0, 1) }}')}
                </div>
            `;
        } else {
            messageWrapper.innerHTML = `
                <div class="w-8 h-8 rounded-xl bg-emerald-800 text-white flex items-center justify-center flex-shrink-0 text-xs shadow-sm">
                    <i data-lucide="bot" class="w-4 h-4 text-emerald-200"></i>
                </div>
                <div class="p-4 rounded-2xl rounded-tl-sm bg-cream-200 text-charcoal text-xs sm:text-sm leading-relaxed border border-stone-200/80 shadow-sm whitespace-pre-line">
                    ${escapeHtml(content)}
                </div>
            `;
        }

        container.appendChild(messageWrapper);
        container.scrollTop = container.scrollHeight;
        lucide.createIcons();
    }

    function appendTypingIndicator() {
        const container = document.getElementById('chatMessages');
        const indicator = document.createElement('div');
        indicator.id = 'typingIndicator';
        indicator.className = 'flex items-start gap-3 max-w-[85%]';
        indicator.innerHTML = `
            <div class="w-8 h-8 rounded-xl bg-emerald-800 text-white flex items-center justify-center flex-shrink-0 text-xs">
                <i data-lucide="bot" class="w-4 h-4 text-emerald-200"></i>
            </div>
            <div class="p-3.5 rounded-2xl bg-cream-200 text-charcoal-muted text-xs border border-stone-200 flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-700 animate-bounce"></span>
                <span class="w-2 h-2 rounded-full bg-emerald-700 animate-bounce" style="animation-delay: 0.2s"></span>
                <span class="w-2 h-2 rounded-full bg-emerald-700 animate-bounce" style="animation-delay: 0.4s"></span>
                <span class="ml-1 text-[11px]">Asisten sedang merumuskan jawaban...</span>
            </div>
        `;
        container.appendChild(indicator);
        container.scrollTop = container.scrollHeight;
        lucide.createIcons();
    }

    function removeTypingIndicator() {
        const el = document.getElementById('typingIndicator');
        if (el) el.remove();
    }

    async function handleChatSubmit(e) {
        e.preventDefault();
        const input = document.getElementById('chatInput');
        const text = input.value.trim();
        if (!text) return;

        input.value = '';
        appendMessage('user', text);
        conversationHistory.push({ role: 'user', content: text });

        appendTypingIndicator();
        document.getElementById('sendBtn').disabled = true;

        try {
            const res = await fetch("{{ route('assistant.chat') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ messages: conversationHistory })
            });

            const data = await res.json();
            removeTypingIndicator();
            document.getElementById('sendBtn').disabled = false;

            if (data.success && data.reply) {
                appendMessage('assistant', data.reply);
                conversationHistory.push({ role: 'assistant', content: data.reply });
            } else {
                appendMessage('assistant', data.error_message || 'Maaf, terjadi kendala saat memproses jawaban.');
            }
        } catch (err) {
            removeTypingIndicator();
            document.getElementById('sendBtn').disabled = false;
            appendMessage('assistant', 'Terjadi kesalahan jaringan. Silakan coba kembali.');
        }
    }

    function sendQuickPrompt(prompt) {
        document.getElementById('chatInput').value = prompt;
        document.getElementById('chatForm').dispatchEvent(new Event('submit'));
    }

    function clearChatHistory() {
        conversationHistory = [];
        const container = document.getElementById('chatMessages');
        container.innerHTML = `
            <div class="flex items-start gap-3 max-w-[85%]">
                <div class="w-8 h-8 rounded-xl bg-emerald-800 text-white flex items-center justify-center flex-shrink-0 text-xs shadow-sm">
                    <i data-lucide="bot" class="w-4 h-4 text-emerald-200"></i>
                </div>
                <div class="p-4 rounded-2xl rounded-tl-sm bg-cream-200 text-charcoal text-xs sm:text-sm leading-relaxed border border-stone-200/80 shadow-sm space-y-2">
                    <p>Percakapan diulang. Silakan tanyakan hal seputar gizi dan panduan nutrisi harian Anda!</p>
                </div>
            </div>
        `;
        lucide.createIcons();
    }

    function escapeHtml(string) {
        const div = document.createElement('div');
        div.innerText = string;
        return div.innerHTML;
    }
</script>
@endsection
