/* =====================================================================
   BBMTANK OLYMPOS — NISAN TALIMI (mini oyun)  24.08.2026
   ---------------------------------------------------------------------
   Batuhan: "mini oyun tarzi bir sey ekleme falan da yapar".
   Oyunun GERCEK mekanigini ogretiyor: aci + guc + ruzgar. Guc barina
   isaret koyup <M> ile otomatik doldurma da birebir ayni sekilde var.
   Bagimlilik yok, tek dosya, <canvas> uzerine elle cizim.
   ===================================================================== */
(function () {
  "use strict";

  var kap = document.getElementById("talim");
  if (!kap) return;

  var cv = kap.querySelector("canvas"),
      cx = cv.getContext("2d"),
      gucSar = kap.querySelector(".guc-sar"),
      gucDol = kap.querySelector(".guc-sar .dol"),
      gucIsaret = kap.querySelector(".guc-sar .isaret"),
      gucEtiket = kap.querySelector(".guc-sar .etiket"),
      etAci = kap.querySelector("[data-aci]"),
      etRuzgar = kap.querySelector("[data-ruzgar]"),
      etAtis = kap.querySelector("[data-atis]"),
      etPuan = kap.querySelector("[data-puan]"),
      etRekor = kap.querySelector("[data-rekor]"),
      perde = kap.querySelector(".perde-son"),
      perdeBaslik = perde.querySelector("h3"),
      perdePuan = perde.querySelector(".puan"),
      perdeYazi = perde.querySelector("p"),
      perdeDugme = perde.querySelector("button"),
      etZeus = kap.querySelector("[data-zeus]");

  /* ---------- dunya ---------- */
  var G = 1000, Y = 470;                 /* ic cozunurluk */
  var YER = Y - 74;                      /* zemin cizgisi */
  var TOP_ATIS = 5;

  var d = {
    aci: 45, guc: 0, isaret: 0, dolduruyor: false, otomatik: false,
    ruzgar: 0, hedef: 700, hedefYuk: 0, atisKalan: TOP_ATIS, puan: 0,
    mermi: null, ucuyor: false, parca: [], duman: [], bitti: false,
    sarsinti: 0, kare: 0
  };

  var REKOR_ANAHTAR = "bbm_talim_rekor";
  var rekor = parseInt(localStorage.getItem(REKOR_ANAHTAR) || "0", 10) || 0;

  var ZEUS_TAM = ["Tam on ikiden!", "İşte bu, Olympos gurur duydu.", "Zeus bile bu kadarını yapamazdı."];
  var ZEUS_YAKIN = ["Az kaldı, rüzgârı biraz daha hesapla.", "Kıyısından geçti.", "Neredeyse. Gücü bir tık oynat."];
  var ZEUS_UZAK = ["Manzarayı vurdun.", "O atış komşu haritaya düştü.", "Rüzgâr seni dinlemiyor galiba."];

  function rast(a, b) { return a + Math.random() * (b - a); }
  function sec(l) { return l[Math.floor(Math.random() * l.length)]; }

  /* ---------- olcek ---------- */
  function olcekle() {
    var op = Math.min(2, window.devicePixelRatio || 1);
    var g = cv.clientWidth || G;
    cv.width = Math.round(g * op);
    cv.height = Math.round(g * (Y / G) * op);
    cx.setTransform(op * (g / G), 0, 0, op * (g / G), 0, 0);
  }
  window.addEventListener("resize", olcekle);

  /* ---------- tur ---------- */
  function yeniTur(tamSifir) {
    if (tamSifir) { d.puan = 0; d.atisKalan = TOP_ATIS; d.bitti = false; }
    d.hedef = rast(430, 900);
    d.hedefYuk = Math.random() < 0.35 ? rast(40, 130) : 0;
    d.ruzgar = Math.round(rast(-45, 45)) / 10;      /* -4.5 .. 4.5 */
    d.mermi = null; d.ucuyor = false; d.guc = 0; d.dolduruyor = false; d.otomatik = false;
    yazdir();
  }

  function yazdir() {
    etAci.textContent = Math.round(d.aci) + "°";
    etRuzgar.textContent = (d.ruzgar > 0 ? "▶ " : d.ruzgar < 0 ? "◀ " : "") +
      Math.abs(d.ruzgar).toFixed(1);
    etAtis.textContent = d.atisKalan;
    etPuan.textContent = d.puan;
    etRekor.textContent = rekor;
    gucDol.style.width = d.guc + "%";
    gucEtiket.textContent = Math.round(d.guc) + "%";
    if (d.isaret > 0) { gucIsaret.style.display = "block"; gucIsaret.style.left = d.isaret + "%"; }
    else gucIsaret.style.display = "none";
  }

  /* ---------- girdi ---------- */
  cv.addEventListener("mousemove", function (e) {
    if (d.ucuyor || d.bitti) return;
    var r = cv.getBoundingClientRect();
    var x = (e.clientX - r.left) * (G / r.width);
    var y = (e.clientY - r.top) * (Y / r.height);
    var dx = Math.max(12, x - 96), dy = (YER - 26) - y;
    d.aci = Math.max(5, Math.min(85, Math.atan2(dy, dx) * 180 / Math.PI));
    yazdir();
  });

  gucSar.addEventListener("click", function (e) {
    if (d.ucuyor || d.bitti) return;
    var r = gucSar.getBoundingClientRect();
    d.isaret = Math.max(2, Math.min(100, (e.clientX - r.left) / r.width * 100));
    yazdir();
    rozetVer("isaret", "\u{1F3AF}", "İşaretçi", "Güç barına işaret koydun. Şimdi M'ye bas.");
  });

  function ates() {
    if (d.ucuyor || d.bitti || d.guc <= 0) return;
    var v = d.guc * 0.185;
    var r = d.aci * Math.PI / 180;
    d.mermi = { x: 96 + Math.cos(r) * 46, y: (YER - 26) - Math.sin(r) * 46,
                vx: Math.cos(r) * v, vy: -Math.sin(r) * v, iz: [] };
    d.ucuyor = true;
    d.dolduruyor = false; d.otomatik = false;
    d.sarsinti = 7;
    d.atisKalan--;
    for (var i = 0; i < 16; i++)
      d.duman.push({ x: d.mermi.x, y: d.mermi.y, vx: rast(0.5, 3.4) * Math.cos(r) + rast(-.7, .7),
                     vy: -rast(0.5, 3.4) * Math.sin(r) + rast(-.7, .7), o: 1, b: rast(3, 9) });
    yazdir();
    if (!window.__talimIlk) { window.__talimIlk = 1; rozetVer("ilkatis", "\u{1F4A5}", "İlk atış", "Talim meydanına hoş geldin."); }
  }

  document.addEventListener("keydown", function (e) {
    if (!gorunurMu()) return;
    if (e.code === "Space") {
      if (d.ucuyor || d.bitti) return;
      e.preventDefault();
      d.dolduruyor = true; d.otomatik = false;
    } else if (e.key === "m" || e.key === "M") {
      if (d.ucuyor || d.bitti || d.isaret <= 0) return;
      e.preventDefault();
      d.otomatik = true; d.dolduruyor = false;
    } else if (e.key === "ArrowUp") {
      if (d.ucuyor || d.bitti) return;
      e.preventDefault(); d.aci = Math.min(85, d.aci + 1); yazdir();
    } else if (e.key === "ArrowDown") {
      if (d.ucuyor || d.bitti) return;
      e.preventDefault(); d.aci = Math.max(5, d.aci - 1); yazdir();
    }
  });
  document.addEventListener("keyup", function (e) {
    if (e.code === "Space" && d.dolduruyor) { d.dolduruyor = false; ates(); }
  });

  /* dokunmatik / fare ile: bara basili tut, birak -> ates */
  var basili = false;
  gucSar.addEventListener("pointerdown", function () {
    if (d.ucuyor || d.bitti) return;
    basili = true; d.guc = 0; d.dolduruyor = true;
  });
  window.addEventListener("pointerup", function () {
    if (basili) { basili = false; d.dolduruyor = false; ates(); }
  });

  perdeDugme.addEventListener("click", function () {
    perde.classList.remove("ac");
    yeniTur(true);
  });

  function gorunurMu() {
    var r = kap.getBoundingClientRect();
    return r.top < window.innerHeight * 0.85 && r.bottom > window.innerHeight * 0.15;
  }

  /* ---------- puanlama ---------- */
  function vurus(x) {
    var uz = Math.abs(x - d.hedef), p = 0, s;
    if (uz <= 16) { p = 100; s = "TAM İSABET"; }
    else if (uz <= 38) { p = 60; s = "ÇOK İYİ"; }
    else if (uz <= 78) { p = 25; s = "YAKIN"; }
    else { p = 0; s = "ISKA"; }
    d.puan += p;
    ucanYazi(x, p ? "+" + p : s);
    if (etZeus) etZeus.textContent = p === 100 ? sec(ZEUS_TAM)
                                    : p > 0    ? sec(ZEUS_YAKIN)
                                               : sec(ZEUS_UZAK);
    if (p === 100) rozetVer("tam", "\u{1F3C6}", "Tam isabet", sec(ZEUS_TAM));
    if (d.atisKalan <= 0) setTimeout(sonuc, 900); else setTimeout(function () { yeniTur(false); }, 900);
    return p;
  }

  function ucanYazi(x, m) {
    var e = document.createElement("div");
    e.className = "ucan";
    e.textContent = m;
    e.style.left = (Math.max(6, Math.min(94, x / G * 100))) + "%";
    e.style.top = ((YER - 60) / Y * 100) + "%";
    kap.querySelector(".sahne").appendChild(e);
    setTimeout(function () { e.remove(); }, 1150);
  }

  function sonuc() {
    d.bitti = true;
    var yeni = d.puan > rekor;
    if (yeni) { rekor = d.puan; localStorage.setItem(REKOR_ANAHTAR, String(rekor)); }
    perdeBaslik.textContent = yeni ? "Yeni rekor!" : "Talim bitti";
    perdePuan.textContent = d.puan;
    perdeYazi.innerHTML = d.puan >= 400
      ? "Olympos'un topçusu sensin. Bu isabetle gerçek meydanda da iş yaparsın."
      : d.puan >= 200
        ? "Fena değil. Rüzgârı bir tık daha erken hesaba katarsan tam isabet gelir."
        : "Rüzgâr yönü ve gücü her turda değişiyor; önce oku, sonra ateş et.";
    perde.classList.add("ac");
    yazdir();
    if (d.puan >= 400) rozetVer("usta", "\u{1F31F}", "Talim ustası", "Tek turda 400 puan. Saygı duyduk.");
  }

  /* ---------- rozet kopru ---------- */
  function rozetVer(k, ikon, baslik, yazi) {
    if (window.olyRozet) window.olyRozet(k, ikon, baslik, yazi);
  }

  /* ---------- cizim ---------- */
  function zemin() {
    var g = cx.createLinearGradient(0, 0, 0, Y);
    g.addColorStop(0, "#12224b"); g.addColorStop(.45, "#1d3f78"); g.addColorStop(1, "#3a5f8f");
    cx.fillStyle = g; cx.fillRect(0, 0, G, Y);

    /* uzak tapinak siluetleri */
    cx.fillStyle = "rgba(244,220,166,.10)";
    for (var i = 0; i < 7; i++) {
      var x = 60 + i * 150, h = 90 + ((i * 37) % 60);
      cx.fillRect(x, YER - h, 56, h);
      cx.fillRect(x - 10, YER - h - 12, 76, 12);
    }
    /* gunes parlamasi */
    var s = cx.createRadialGradient(G * .5, 60, 10, G * .5, 60, 320);
    s.addColorStop(0, "rgba(255,240,200,.35)"); s.addColorStop(1, "transparent");
    cx.fillStyle = s; cx.fillRect(0, 0, G, 380);

    /* mermer zemin */
    var z = cx.createLinearGradient(0, YER, 0, Y);
    z.addColorStop(0, "#efe6d2"); z.addColorStop(1, "#b9a884");
    cx.fillStyle = z; cx.fillRect(0, YER, G, Y - YER);
    cx.strokeStyle = "rgba(212,162,81,.55)"; cx.lineWidth = 3;
    cx.beginPath(); cx.moveTo(0, YER); cx.lineTo(G, YER); cx.stroke();
    cx.strokeStyle = "rgba(138,90,30,.28)"; cx.lineWidth = 1;
    for (var k = 0; k < 22; k++) {
      cx.beginPath(); cx.moveTo(k * 48, YER); cx.lineTo(k * 48 - 26, Y); cx.stroke();
    }
  }

  function topcu() {
    var r = d.aci * Math.PI / 180;
    /* golge */
    cx.fillStyle = "rgba(0,0,0,.22)";
    cx.beginPath(); cx.ellipse(96, YER + 6, 44, 9, 0, 0, 7); cx.fill();
    /* govde */
    var g = cx.createLinearGradient(60, YER - 40, 130, YER);
    g.addColorStop(0, "#2f6dbf"); g.addColorStop(1, "#0e2f5c");
    cx.fillStyle = g;
    cx.beginPath(); cx.roundRect(58, YER - 34, 78, 34, 9); cx.fill();
    cx.strokeStyle = "#d4a251"; cx.lineWidth = 2.5; cx.stroke();
    /* namlu */
    cx.save();
    cx.translate(96, YER - 26); cx.rotate(-r);
    var n = cx.createLinearGradient(0, -7, 0, 7);
    n.addColorStop(0, "#f4dca6"); n.addColorStop(.5, "#d4a251"); n.addColorStop(1, "#8a5a1e");
    cx.fillStyle = n;
    cx.beginPath(); cx.roundRect(0, -7, 62, 14, 6); cx.fill();
    cx.strokeStyle = "#503010"; cx.lineWidth = 2; cx.stroke();
    cx.restore();
    /* burc */
    cx.fillStyle = "#f4dca6";
    cx.beginPath(); cx.arc(96, YER - 26, 13, 0, 7); cx.fill();
    cx.strokeStyle = "#503010"; cx.lineWidth = 2.5; cx.stroke();

    /* nisan cizgisi */
    if (!d.ucuyor && !d.bitti) {
      cx.save();
      cx.setLineDash([7, 9]); cx.strokeStyle = "rgba(244,220,166,.5)"; cx.lineWidth = 2;
      cx.beginPath(); cx.moveTo(96, YER - 26);
      cx.lineTo(96 + Math.cos(r) * 190, (YER - 26) - Math.sin(r) * 190);
      cx.stroke(); cx.restore();
    }
  }

  function hedef() {
    var x = d.hedef, ty = YER - d.hedefYuk;
    if (d.hedefYuk > 0) {           /* sutun */
      var s = cx.createLinearGradient(x - 20, ty, x + 20, YER);
      s.addColorStop(0, "#f6f1e6"); s.addColorStop(1, "#c7b79a");
      cx.fillStyle = s; cx.fillRect(x - 17, ty, 34, d.hedefYuk);
      cx.fillStyle = "#e8dcc0"; cx.fillRect(x - 24, ty - 10, 48, 12);
    }
    /* kalkan hedefi */
    var kad = 26 + Math.sin(d.kare / 22) * 2;
    cx.save(); cx.translate(x, ty - 30);
    var halka = ["#c0392b", "#f6f1e6", "#1a4d8c", "#f4dca6"];
    for (var i = 0; i < 4; i++) {
      cx.beginPath(); cx.arc(0, 0, kad - i * 6, 0, 7);
      cx.fillStyle = halka[i]; cx.fill();
    }
    cx.strokeStyle = "#503010"; cx.lineWidth = 2.5;
    cx.beginPath(); cx.arc(0, 0, kad, 0, 7); cx.stroke();
    cx.restore();
    /* ruzgar bayragi */
    cx.save(); cx.translate(x, ty - 30 - kad - 26);
    cx.strokeStyle = "rgba(244,220,166,.8)"; cx.lineWidth = 2;
    cx.beginPath(); cx.moveTo(0, 0); cx.lineTo(0, 22); cx.stroke();
    var yon = d.ruzgar >= 0 ? 1 : -1, uz = Math.min(30, Math.abs(d.ruzgar) * 7);
    cx.fillStyle = "rgba(144,206,238,.9)";
    cx.beginPath(); cx.moveTo(0, 1); cx.lineTo(yon * uz, 7); cx.lineTo(0, 13); cx.fill();
    cx.restore();
  }

  function mermiCiz() {
    if (!d.mermi) return;
    var m = d.mermi;
    /* iz */
    cx.strokeStyle = "rgba(144,206,238,.55)"; cx.lineWidth = 3; cx.lineCap = "round";
    cx.beginPath();
    for (var i = 0; i < m.iz.length; i++) {
      var p = m.iz[i];
      if (i === 0) cx.moveTo(p.x, p.y); else cx.lineTo(p.x, p.y);
    }
    cx.stroke();
    /* golge alan */
    var g = cx.createRadialGradient(m.x, m.y, 1, m.x, m.y, 16);
    g.addColorStop(0, "#fff"); g.addColorStop(.4, "#90ceee"); g.addColorStop(1, "transparent");
    cx.fillStyle = g;
    cx.beginPath(); cx.arc(m.x, m.y, 16, 0, 7); cx.fill();
    cx.fillStyle = "#f4dca6";
    cx.beginPath(); cx.arc(m.x, m.y, 6, 0, 7); cx.fill();
  }

  function efektler() {
    var i, p;
    for (i = d.parca.length - 1; i >= 0; i--) {
      p = d.parca[i];
      p.x += p.vx; p.y += p.vy; p.vy += 0.34; p.o -= 0.019;
      if (p.o <= 0) { d.parca.splice(i, 1); continue; }
      cx.globalAlpha = Math.max(0, p.o);
      cx.fillStyle = p.r;
      cx.beginPath(); cx.arc(p.x, p.y, p.b, 0, 7); cx.fill();
      cx.globalAlpha = 1;
    }
    for (i = d.duman.length - 1; i >= 0; i--) {
      p = d.duman[i];
      p.x += p.vx; p.y += p.vy; p.vx *= .96; p.vy *= .96; p.o -= 0.028; p.b += .5;
      if (p.o <= 0) { d.duman.splice(i, 1); continue; }
      cx.globalAlpha = Math.max(0, p.o) * .45;
      cx.fillStyle = "#e8e2d2";
      cx.beginPath(); cx.arc(p.x, p.y, p.b, 0, 7); cx.fill();
      cx.globalAlpha = 1;
    }
  }

  function patlama(x, y, tam) {
    var renk = tam ? ["#f4dca6", "#d4a251", "#fff2c9"] : ["#90ceee", "#2f6dbf", "#e8e2d2"];
    for (var i = 0; i < (tam ? 46 : 26); i++) {
      var a = rast(0, Math.PI * 2), v = rast(1.5, 8);
      d.parca.push({ x: x, y: y, vx: Math.cos(a) * v, vy: Math.sin(a) * v - 2,
                     o: 1, b: rast(2, 6), r: sec(renk) });
    }
    d.sarsinti = tam ? 14 : 8;
  }

  /* ---------- dongu ---------- */
  function dongu() {
    d.kare++;

    /* guc dolumu */
    if (!d.ucuyor && !d.bitti) {
      if (d.otomatik) {
        d.guc = Math.min(100, d.guc + 1.9);
        if (d.guc >= d.isaret) { d.guc = d.isaret; ates(); }
        else yazdir();
      } else if (d.dolduruyor) {
        d.guc = Math.min(100, d.guc + 1.6);
        yazdir();
      }
    }

    /* fizik */
    if (d.ucuyor && d.mermi) {
      var m = d.mermi;
      for (var k = 0; k < 2; k++) {                /* alt adim: daha puruzsuz */
        m.vy += 0.115;
        m.vx += d.ruzgar * 0.0075;
        m.x += m.vx * .5; m.y += m.vy * .5;
      }
      m.iz.push({ x: m.x, y: m.y });
      if (m.iz.length > 26) m.iz.shift();

      var hy = YER - d.hedefYuk - 30;
      var carpti = false, tam = false;
      if (Math.abs(m.x - d.hedef) < 30 && Math.abs(m.y - hy) < 30) { carpti = true; tam = true; }
      else if (m.y >= YER) { carpti = true; }
      else if (m.x > G + 90 || m.x < -90 || m.y > Y + 200) {
        d.ucuyor = false; d.mermi = null; vurus(m.x > G ? G + 200 : -200); carpti = false;
      }
      if (carpti) {
        d.ucuyor = false;
        patlama(m.x, Math.min(m.y, YER), tam);
        var vx = m.x; d.mermi = null;
        vurus(tam ? d.hedef : vx);
      }
    }

    /* ciz */
    cx.save();
    if (d.sarsinti > 0) {
      cx.translate(rast(-d.sarsinti, d.sarsinti) * .5, rast(-d.sarsinti, d.sarsinti) * .5);
      d.sarsinti *= .84;
      if (d.sarsinti < .4) d.sarsinti = 0;
    }
    zemin(); hedef(); topcu(); mermiCiz(); efektler();
    cx.restore();

    requestAnimationFrame(dongu);
  }

  /* roundRect yoksa (eski tarayici) basit yedek */
  if (!CanvasRenderingContext2D.prototype.roundRect) {
    CanvasRenderingContext2D.prototype.roundRect = function (x, y, w, h, r) {
      this.beginPath();
      this.moveTo(x + r, y);
      this.arcTo(x + w, y, x + w, y + h, r);
      this.arcTo(x + w, y + h, x, y + h, r);
      this.arcTo(x, y + h, x, y, r);
      this.arcTo(x, y, x + w, y, r);
      this.closePath();
      return this;
    };
  }

  olcekle();
  yeniTur(true);
  requestAnimationFrame(dongu);
})();
