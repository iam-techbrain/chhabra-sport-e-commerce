<template>
  <section class="section" id="services">
    <div class="wrap">
      <div class="stringing-banner">
        <div class="stringing-content">
          <span class="eyebrow">CHHABRA SPORTS WORKSHOP</span>
          <h3>ELECTRONIC STRINGING & BAT KNOCKING</h3>
          <p>Get your badminton or tennis racquets strung on our computerized Yonex ES5PROTECH stringing machine. Choose your string type, custom tension (22 to 32 lbs), and express 60-min turnaround.</p>
          
          <div class="spec-options-grid">
            <div class="spec-opt">
              <b>YONEX ES5</b>
              <span>Digital Precision Machine</span>
            </div>
            <div class="spec-opt">
              <b>24 HOUR</b>
              <span>Express Pan-India Pickup</span>
            </div>
            <div class="spec-opt">
              <b>BAT OILING</b>
              <span>5000+ Stroke Knocking</span>
            </div>
          </div>

          <button class="btn btn-gold" @click="bookingAlert">BOOK WORKSHOP SLOT →</button>
        </div>

        <div class="stringing-visual">
          <svg viewBox="0 0 100 100" fill="none" stroke="var(--gold)" stroke-width="2">
            <ellipse cx="50" cy="40" rx="30" ry="38" />
            <line x1="50" y1="78" x2="50" y2="98" stroke-width="4"/>
            <line x1="26" y1="40" x2="74" y2="40" stroke-dasharray="2 2"/>
            <line x1="30" y1="25" x2="70" y2="25" stroke-dasharray="2 2"/>
            <line x1="30" y1="55" x2="70" y2="55" stroke-dasharray="2 2"/>
            <line x1="50" y1="5" x2="50" y2="75" stroke-dasharray="2 2"/>
            <line x1="38" y1="10" x2="38" y2="70" stroke-dasharray="2 2"/>
            <line x1="62" y1="10" x2="62" y2="70" stroke-dasharray="2 2"/>
          </svg>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
function bookingAlert() {
  alert("Pro Stringing & Bat Knocking Booking\n\nPlease call our workshop team at +91-72772-52440 or WhatsApp us to book express stringing!");
}
</script>
