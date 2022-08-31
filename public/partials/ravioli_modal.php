<div class="ravioli--background" id="ravioli--background">
  <div class="ravioli--modal">
    <h3>Mehrwegversandbox (von Ravioli)</h3>
    <img src="<?php echo plugins_url( '/public/img/ravioli_return.jpeg', dirname(__DIR__, 1) ); ?>" id="ravioli--pic"/>
    <p>
      Erhalte deine Bestellung in einer nachhaltigen Mehrwegversandbox von Ravioli
      für zusätzlich <?php echo wc_price(esc_html(trim(get_option( 'ravioli_settings_tab_fee' )))) ?>.
      Du hilfst so dabei mit Müll und CO2-Emissionen zu reduzieren.
      Deine Ravioli Box kannst du anschliessend bequem und kostenlos in jedem DHL Paketshop zurückbringen.
    </p>
    <div class="ravioli--button-container">
      <button class="ravioli--button button button alt" id="ravioli--button-yes">Ja, gerne</button>
      <button class="ravioli--button ravioli--button-secondary button" id="ravioli--button-no">Nein, danke</button>
    </div>
  </div>
</div>