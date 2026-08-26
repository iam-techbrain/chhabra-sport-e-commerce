<template>
  <div class="about-page-wrap">
    <!-- HERO QUOTE SECTION -->
    <section class="section" style="background:var(--card-bg, #FAF8F5); padding: 60px 0; border-bottom:1px solid var(--line);">
      <div class="wrap">
        <div class="about-hero-grid">
          <div class="quote-card">
            <div class="quote-author">
              <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?w=200&q=80&auto=format&fit=crop" alt="Rahul Chhabra" class="author-img">
              <div>
                <span class="by-text">By</span> <strong>Rahul Chhabra</strong>
                <div class="sig-font">Rahul Chhabra</div>
              </div>
            </div>
            <h2 class="hero-quote-text">
              "The right sports equipment is more than just gear — It's your shield, strength, and strategy on the field."
            </h2>
          </div>

          <div class="perks-column">
            <div class="perk-box">
              <div class="perk-icon-wrap">
                <svg viewBox="0 0 24 24" width="28" height="28" stroke="var(--gold-dark, #8A6818)" fill="none" stroke-width="2">
                  <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                </svg>
              </div>
              <div>
                <h4 style="color:var(--oxblood, #6B1D2F); font-family:'Outfit',sans-serif; font-size:18px; font-weight:800;">Happy Athletes</h4>
                <p style="font-size:13px; font-family:'Space Mono',monospace; color:var(--ink-soft); margin-top:4px; line-height:1.5;">The joy of the game begins with the right gear — trusted by thousands across generations.</p>
              </div>
            </div>

            <div class="perk-box">
              <div class="perk-icon-wrap">
                <svg viewBox="0 0 24 24" width="28" height="28" stroke="var(--gold-dark, #8A6818)" fill="none" stroke-width="2">
                  <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                </svg>
              </div>
              <div>
                <h4 style="color:var(--oxblood, #6B1D2F); font-family:'Outfit',sans-serif; font-size:18px; font-weight:800;">Top-Tier Brands</h4>
                <p style="font-size:13px; font-family:'Space Mono',monospace; color:var(--ink-soft); margin-top:4px; line-height:1.5;">Only the best make the cut — trusted names, pro-level performance, and gear you can count on.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- OUR LEGACY SECTION (WITH CRICKET MATCH & FOOTBALLER IMAGES) -->
    <section class="section" style="padding: 80px 0;">
      <div class="wrap">
        <div class="legacy-grid">
          <div class="legacy-images">
            <img src="https://images.unsplash.com/photo-1531415074968-036ba1b575da?w=800&q=80&auto=format&fit=crop" alt="Cricket Match Legacy" class="leg-img-1">
            <img src="https://images.unsplash.com/photo-1508098682722-e99c43a406b2?w=800&q=80&auto=format&fit=crop" alt="Young Footballer Legacy" class="leg-img-2">
          </div>

          <div class="legacy-text">
            <div class="eyebrow" style="color:var(--gold-dark, #8A6818);">ESTABLISHED 1948</div>
            <h2 style="color:var(--ink, #111); font-family:'Outfit',sans-serif; font-size:36px; font-weight:900; margin:6px 0 20px;">Our Legacy</h2>
            <p class="legacy-paragraph">
              <strong>CHHABRA SPORTS AGENCIES</strong> is a family-owned sports goods retail business with one of the biggest portfolios of brands to offer. With its inception in 1948, Chhabra Sports was one of the initial players to enter the sports retail business in Patna, Bihar.
            </p>
            <p class="legacy-paragraph">
              The business was founded by late Mr. C.D Chhabra who had moved to India during partition from Sialkot, (now Pakistan) which was then the sports manufacturing hub of undivided India. With this belief and blessing, Mr. Jogesh Chhabra and his sons Mr. Rahul Chhabra and Harsh Chhabra have taken the business forward to online sales with the vision to break geographical barriers and cater globally to all sports enthusiast and professional through our website.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- CORE VALUES SECTION (WITH SPORTS EQUIPMENT WALL IMAGE) -->
    <section class="section" style="background:var(--pitch-dark, #0A241C); color:#fff; padding: 80px 0;">
      <div class="wrap">
        <div class="values-grid">
          <div class="values-content">
            <span class="eyebrow" style="color:var(--gold-light, #E3C174); letter-spacing:1.5px;">WHAT WE STAND FOR</span>
            <h2 style="color:#ffffff; font-family:'Outfit',sans-serif; font-size:36px; font-weight:900; margin:10px 0 24px;">Core Values</h2>
            <p style="font-size:15px; font-family:'Space Mono',monospace; color:rgba(255,255,255,0.8); margin-bottom:24px;">
              Chhabra Sports Agencies is built on four founding principles:
            </p>

            <ol class="values-list">
              <li><strong>1. Personalized experience to clients.</strong></li>
              <li><strong>2. Building and retaining a strong and loyal workforce</strong> who have played an important role in the success of our business.</li>
              <li><strong>3. Maintaining trust and long-term relationship</strong> with our customers.</li>
              <li><strong>4. A strong after-sales support</strong> with whatever max we can help.</li>
            </ol>

            <button class="btn btn-gold" @click="$emit('nav-shop')" style="margin-top:30px;">VIEW CATALOG →</button>
          </div>

          <div class="values-image-wrap">
            <img src="https://images.unsplash.com/photo-1584735935682-2f2b69dff9d2?w=800&q=80&auto=format&fit=crop" alt="Sports Equipment Core Values" class="values-img">
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
defineEmits(['nav-shop']);
</script>

<style scoped>
.about-page-wrap {
  background: var(--bg, #EFECE6);
  color: var(--ink, #111);
}

.about-hero-grid {
  display: grid;
  grid-template-columns: 1.2fr 0.8fr;
  gap: 40px;
  align-items: center;
}

.quote-card {
  background: #ffffff;
  padding: 36px;
  border-radius: 8px;
  border: 1px solid var(--line);
  box-shadow: var(--shadow-sm);
}

.quote-author {
  display: flex;
  align-items: center;
  gap: 16px;
  margin-bottom: 24px;
}

.author-img {
  width: 64px;
  height: 64px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid var(--gold);
}

.by-text {
  color: var(--ink-soft);
  font-size: 13px;
  font-family: 'Space Mono', monospace;
}

.sig-font {
  font-family: 'Space Mono', monospace;
  font-style: italic;
  font-size: 15px;
  color: var(--oxblood, #6B1D2F);
  font-weight: 700;
}

.hero-quote-text {
  font-family: 'Outfit', sans-serif;
  font-size: 24px;
  font-weight: 800;
  color: var(--pitch-dark, #0A241C);
  line-height: 1.4;
}

.perks-column {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.perk-box {
  background: #ffffff;
  padding: 22px;
  border-radius: 8px;
  border: 1px solid var(--line);
  display: flex;
  gap: 18px;
  align-items: flex-start;
  box-shadow: var(--shadow-sm);
}

.perk-icon-wrap {
  background: rgba(203, 163, 78, 0.15);
  padding: 12px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.legacy-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 50px;
  align-items: center;
}

.legacy-images {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.leg-img-1, .leg-img-2 {
  width: 100%;
  height: 380px;
  object-fit: cover;
  border-radius: 8px;
  border: 1px solid var(--line);
  box-shadow: 0 4px 16px rgba(0,0,0,0.08);
}

.legacy-paragraph {
  font-size: 15px;
  line-height: 1.7;
  color: var(--ink-soft);
  margin-bottom: 16px;
}

.values-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 50px;
  align-items: center;
}

.values-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.values-list li {
  font-size: 14px;
  font-family: 'Space Mono', monospace;
  color: rgba(255,255,255,0.9);
  margin-bottom: 16px;
  line-height: 1.6;
  padding-left: 14px;
  border-left: 3px solid var(--gold-light, #E3C174);
}

.values-img {
  width: 100%;
  max-height: 440px;
  object-fit: cover;
  border-radius: 8px;
  border: 2px solid rgba(227,193,116,0.3);
  box-shadow: 0 8px 24px rgba(0,0,0,0.3);
}

@media (max-width: 900px) {
  .about-hero-grid, .legacy-grid, .values-grid {
    grid-template-columns: 1fr;
  }
}
</style>
