<template>
  <div>
    <!-- HERO SECTION -->
    <section class="hero">
      <div class="hero-copy">
        <span class="eyebrow display">India's Preferred Online Racquet & Sports Store</span>
        <h1>Play Hard.<br><em>Perform</em> <span class="highlight">Better.</span></h1>
        <p>Authentic Yonex, Head, Babolat & Li-Ning racquets, SS English Willow cricket bats, tournament shoes and
          certified stringing — trusted by athletes across India since 1998.</p>
        <div class="hero-ctas">
          <a href="#products" class="btn btn-gold" @click.prevent="$emit('filter-cat', 'all')">
            Shop Catalog
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="5" y1="12" x2="19" y2="12" />
              <polyline points="12 5 19 12 12 19" />
            </svg>
          </a>
          <button @click="$emit('open-racquet-finder')" class="btn btn-outline">⚡ Launch Racquet Finder</button>
        </div>
      </div>

      <div class="hero-visual">
        <div class="hero-stitch"></div>
        <img class="hero-bg"
          src="https://images.unsplash.com/photo-1531415074968-036ba1b575da?w=1200&q=80&auto=format&fit=crop"
          alt="Athlete in action">

        <!-- Racquet Finder Widget Badge -->
        <div class="racquet-finder-badge hide-mobile" @click="$emit('open-racquet-finder')">
          <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" fill="none" stroke-width="2">
            <circle cx="12" cy="12" r="10" />
            <polygon points="12 8 8 16 16 16" />
          </svg>
          <div>
            <div class="rf-title">Racquet Finder Tool</div>
            <div class="rf-sub">Find your perfect frame in 3 steps →</div>
          </div>
        </div>
      </div>
    </section>

    <!-- SCOREBOARD TICKER -->
    <div class="scoreboard">
      <div class="scoreboard-track">
        <div class="score-item"><span class="num">25+</span><span class="lbl">Years in Sport</span></div>
        <div class="score-item"><span class="num">12,000+</span><span class="lbl">Products Stocked</span></div>
        <div class="score-item"><span class="num">100%</span><span class="lbl">Genuine Warranty</span></div>
        <div class="score-item"><span class="num">500+</span><span class="lbl">Academies Equipped</span></div>
        <div class="score-item"><span class="num">60 MIN</span><span class="lbl">Pro Stringing Service</span></div>
        <div class="score-item"><span class="num">4.8/5</span><span class="lbl">Rated by Players</span></div>
        <!-- Duplicate for smooth loop -->
        <div class="score-item"><span class="num">25+</span><span class="lbl">Years in Sport</span></div>
        <div class="score-item"><span class="num">12,000+</span><span class="lbl">Products Stocked</span></div>
        <div class="score-item"><span class="num">100%</span><span class="lbl">Genuine Warranty</span></div>
        <div class="score-item"><span class="num">500+</span><span class="lbl">Academies Equipped</span></div>
        <div class="score-item"><span class="num">60 MIN</span><span class="lbl">Pro Stringing Service</span></div>
        <div class="score-item"><span class="num">4.8/5</span><span class="lbl">Rated by Players</span></div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineEmits(['filter-cat', 'open-racquet-finder']);
</script>
