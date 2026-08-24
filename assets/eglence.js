/* =====================================================================
   BBMTANK OLYMPOS — EGLENCE KATMANI  (24.08.2026)
   ---------------------------------------------------------------------
   Batuhan: "kurumsal site gibi durmasin, daha eglenceli ve bol
   animasyonlu/efektli olsun, interaktif olsun."
     1) Rozet bildirimleri (window.olyRozet ile her yerden cagrilir)
     2) Hero'ya tiklayinca gercek simsek carpmasi + ekran flasi
     3) Dugmelerde kivilcim patlamasi
     4) Arka planda suzulen bulut katmani
     5) Gizli kod (yukari yukari asagi asagi sol sag sol sag B A) -> altin yagmuru
     6) Levhalari okudukca ilerleyen kesif rozeti
   ===================================================================== */
(function () {
  "use strict";

  var azHareket = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  /* ================= 1) ROZETLER ================= */
  var ANAHTAR = "bbm_rozetler";
  var alinan = {};
  try { alinan = JSON.parse(localStorage.getItem(ANAHTAR) || "{}"); } catch (e) { alinan = {}; }

  var yuva = document.createElement("div");
  yuva.className = "oly-rozetler";
  document.body.appendChild(yuva);

  window.olyRozet = function (kod, ikon, baslik, yazi) {
    if (alinan[kod]) return;
    alinan[kod] = 1;
    try { localStorage.setItem(ANAHTAR, JSON.stringify(alinan)); } catch (e) {}

    var e = document.createElement("div");
    e.className = "oly-rozet";
    e.innerHTML = '<span class="mad">' + ikon + '</span><div><b>' + baslik + '</b><span>' + yazi + '</span></div>';
    yuva.appendChild(e);
    setTimeout(function () {
      e.classList.add("cik");
      setTimeout(function () { e.remove(); }, 500);
    }, 5200);
  };

  /* ================= 2) SIMSEK ================= */
  var tuval = null, ctx = null, flas = null;

  function simsekKur() {
    tuval = document.createElement("canvas");
    tuval.id = "olyCakma";
    document.body.appendChild(tuval);
    ctx = tuval.getContext("2d");
    flas = document.createElement("div");
    flas.className = "oly-flas";
    document.body.appendChild(flas);
    boyutla();
    window.addEventListener("resize", boyutla);
  }
  function boyutla() {
    if (!tuval) return;
    tuval.width = window.innerWidth;
    tuval.height = window.innerHeight;
  }

  function dalCiz(x1, y1, x2, y2, sapma, kalinlik, derinlik) {
    if (sapma < 4 || derinlik > 5) {
      ctx.lineWidth = kalinlik;
      ctx.beginPath(); ctx.moveTo(x1, y1); ctx.lineTo(x2, y2); ctx.stroke();
      return;
    }
    var ox = (x1 + x2) / 2 + (Math.random() - .5) * sapma;
    var oy = (y1 + y2) / 2 + (Math.random() - .5) * sapma * .35;
    dalCiz(x1, y1, ox, oy, sapma / 2, kalinlik, derinlik + 1);
    dalCiz(ox, oy, x2, y2, sapma / 2, kalinlik, derinlik + 1);
    if (derinlik < 3 && Math.random() < .45) {
      dalCiz(ox, oy, ox + (Math.random() - .5) * 130, oy + Math.random() * 110,
             sapma / 2, Math.max(1, kalinlik - 1.2), derinlik + 2);
    }
  }

  function simsekCak(x, y) {
    if (azHareket) return;
    if (!tuval) simsekKur();
    var kare = 0;
    flas.classList.remove("cak");
    void flas.offsetWidth;
    flas.classList.add("cak");

    function ciz() {
      ctx.clearRect(0, 0, tuval.width, tuval.height);
      if (kare > 13) { ctx.clearRect(0, 0, tuval.width, tuval.height); return; }
      ctx.globalAlpha = kare < 4 ? 1 : Math.max(0, 1 - (kare - 4) / 9);
      ctx.lineCap = "round";
      ctx.strokeStyle = "rgba(144,206,238,.55)";
      dalCiz(x, -20, x + (Math.random() - .5) * 60, y, 190, 9, 0);
      ctx.strokeStyle = "#eaf7ff";
      dalCiz(x, -20, x + (Math.random() - .5) * 40, y, 150, 3.2, 0);
      ctx.globalAlpha = 1;
      kare++;
      requestAnimationFrame(ciz);
    }
    ciz();
  }
  window.olySimsek = simsekCak;

  var hero = document.querySelector(".oly-hero");
  if (hero) {
    hero.addEventListener("click", function (e) {
      if (e.target.closest("a,button,input,select,label,form,.giris-levha")) return;
      simsekCak(e.clientX, e.clientY);
      window.olyRozet("simsek", "⚡", "Zeus'un dikkatini çektin",
                      "Sahneye şimşek indirdin. Bir daha dene, her seferinde farklı.");
    });
  }

  /* ================= 3) KIVILCIM ================= */
  document.addEventListener("click", function (e) {
    if (azHareket) return;
    var d = e.target.closest ? e.target.closest(".btn") : null;
    if (!d) return;
    for (var i = 0; i < 14; i++) {
      (function () {
        var p = document.createElement("span");
        p.className = "oly-kivilcim";
        p.style.left = e.clientX + "px";
        p.style.top = e.clientY + "px";
        document.body.appendChild(p);
        var a = Math.random() * Math.PI * 2, v = 30 + Math.random() * 70;
        var x = Math.cos(a) * v, y = Math.sin(a) * v - 22;
        p.animate([
          { transform: "translate(0,0) scale(1)", opacity: 1 },
          { transform: "translate(" + x + "px," + y + "px) scale(.2)", opacity: 0 }
        ], { duration: 480 + Math.random() * 320, easing: "cubic-bezier(.2,.7,.3,1)" });
        setTimeout(function () { p.remove(); }, 850);
      })();
    }
  });

  /* ================= 4) BULUTLAR ================= */
  if (!azHareket) {
    var b = document.createElement("div");
    b.className = "oly-bulut";
    for (var i = 0; i < 7; i++) {
      var c = document.createElement("i");
      var g = 260 + Math.random() * 360;
      c.style.width = g + "px";
      c.style.height = (g * .42) + "px";
      c.style.top = (Math.random() * 85) + "vh";
      c.style.animationDuration = (58 + Math.random() * 70) + "s";
      c.style.animationDelay = (-Math.random() * 90) + "s";
      b.appendChild(c);
    }
    document.body.appendChild(b);
  }

  /* ================= 5) GIZLI KOD ================= */
  var KOD = ["ArrowUp", "ArrowUp", "ArrowDown", "ArrowDown",
             "ArrowLeft", "ArrowRight", "ArrowLeft", "ArrowRight", "b", "a"];
  var sira = 0;
  document.addEventListener("keydown", function (e) {
    var t = e.key.length === 1 ? e.key.toLowerCase() : e.key;
    if (t === KOD[sira]) {
      sira++;
      if (sira === KOD.length) { sira = 0; altinYagmuru(); }
    } else {
      sira = (t === KOD[0]) ? 1 : 0;
    }
  });

  function altinYagmuru() {
    var y = document.createElement("div");
    y.className = "oly-yagmur";
    document.body.appendChild(y);
    var simge = ["\u{1FA99}", "✨", "⚡", "\u{1F3C6}"];
    for (var i = 0; i < 90; i++) {
      var e = document.createElement("i");
      e.textContent = simge[i % simge.length];
      e.style.left = (Math.random() * 100) + "vw";
      e.style.fontSize = (16 + Math.random() * 26) + "px";
      e.style.animationDuration = (2.4 + Math.random() * 3.4) + "s";
      e.style.animationDelay = (Math.random() * 1.6) + "s";
      y.appendChild(e);
    }
    setTimeout(function () { y.remove(); }, 7000);
    window.olyRozet("gizli", "\u{1F451}", "Gizli kodu buldun",
                    "Olympos'un kasası bir anlığına devrildi. Kimseye söyleme.");
  }

  /* ================= 6) LEVHA OKUMA ROZETI ================= */
  var okunan = {};
  document.addEventListener("click", function (e) {
    var l = e.target.closest ? e.target.closest("[data-modal]") : null;
    if (!l) return;
    okunan[l.getAttribute("data-baslik") || String(Math.random())] = 1;
    var n = 0, k;
    for (k in okunan) if (okunan.hasOwnProperty(k)) n++;
    if (n >= 3) {
      window.olyRozet("meraklı", "\u{1F4DC}", "Meraklı okur",
                      "Üç levhayı açıp okudun. Rehberde daha fazlası var.");
    }
  });
})();
