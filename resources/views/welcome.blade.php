@extends('layouts.app')

@section('content')
<style>
    /* سنضع الكود هنا للتبسيط، ويفضل نقله لملف منفصل لاحقاً */
    .glass-card {
        background: rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .text-glow {
        text-shadow: 0 0 20px rgba(99, 102, 241, 0.5);
    }
    #canvas-bg {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1;
    }
</style>

<div class="relative min-h-[85vh] flex items-center justify-center overflow-hidden font-sans">
    <canvas id="canvas-bg"></canvas>

    <div class="relative z-10 max-w-4xl mx-auto px-6 text-center">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/10 border border-indigo-500/30 mb-8 animate-bounce">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
            </span>
            <span class="text-xs font-bold tracking-widest text-indigo-400 uppercase">System Online: v2.0</span>
        </div>

        <h1 class="text-6xl md:text-8xl font-black mb-6 text-white tracking-tight leading-none">
            Master the <br>
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 via-purple-400 to-pink-400 text-glow">
                Dev Quiz
            </span>
        </h1>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
            <a href="{{ route('login') }}" class="group relative inline-flex items-center justify-center px-10 py-4 font-extrabold text-white transition-all duration-300 bg-[#5865F2] rounded-2xl hover:bg-[#4752C4] hover:shadow-[0_0_30px_rgba(88,101,242,0.5)] hover:-translate-y-1">
                <svg class="w-6 h-6 mr-3 transform group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028 14.09 14.09 0 0 0 1.226-1.994.076.076 0 0 0-.041-.106 13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128 10.2 10.2 0 0 0 .372-.292.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127 12.299 12.299 0 0 1-1.873.892.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418z"/>
                </svg>
                Join with Discord
            </a>
            
           
        </div>

        <div class="mt-20 grid grid-cols-2 md:grid-cols-4 gap-4 px-4">
            <div class="glass-card p-4 rounded-2xl">
                <span class="block text-2xl font-black text-indigo-400">+{{ $membersCount }}</span>
                <span class="text-xs text-gray-500 uppercase tracking-widest">Members</span>
            </div>
            <div class="glass-card p-4 rounded-2xl">
                <span class="block text-2xl font-black text-purple-400">{{ $badgesCount }}</span>
                <span class="text-xs text-gray-500 uppercase tracking-widest">Badges Earned</span>
            </div>
            <div class="glass-card p-4 rounded-2xl">
                <span class="block text-2xl font-black text-pink-400">24/7</span>
                <span class="text-xs text-gray-500 uppercase tracking-widest">Support</span>
            </div>
            <div class="glass-card p-4 rounded-2xl">
                <span class="block text-2xl font-black text-blue-400">Free</span>
                <span class="text-xs text-gray-500 uppercase tracking-widest">To Play</span>
            </div>
        </div>
    </div>
</div>

<script>
    const canvas = document.getElementById('canvas-bg');
    const ctx = canvas.getContext('2d');
    let particles = [];

    function init() {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    }

    class Particle {
        constructor() {
            this.x = Math.random() * canvas.width;
            this.y = Math.random() * canvas.height;
            this.size = Math.random() * 2 + 0.1;
            this.speedX = Math.random() * 1 - 0.5;
            this.speedY = Math.random() * 1 - 0.5;
            this.color = 'rgba(99, 102, 241, ' + Math.random() * 0.5 + ')';
        }
        update() {
            this.x += this.speedX;
            this.y += this.speedY;
            if (this.x > canvas.width) this.x = 0;
            if (this.x < 0) this.x = canvas.width;
            if (this.y > canvas.height) this.y = 0;
            if (this.y < 0) this.y = canvas.height;
        }
        draw() {
            ctx.fillStyle = this.color;
            ctx.beginPath();
            ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
            ctx.fill();
        }
    }

    function createParticles() {
        for (let i = 0; i < 100; i++) {
            particles.push(new Particle());
        }
    }

    function animate() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        particles.forEach(p => {
            p.update();
            p.draw();
        });
        requestAnimationFrame(animate);
    }

    window.addEventListener('resize', init);
    init();
    createParticles();
    animate();
</script>
@endsection