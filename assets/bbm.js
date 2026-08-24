/* =====================================================================
   BBMTANK OLYMPOS — ETKILESIM KATMANI v2 (24.08.2026)
   Batuhan: "asiri eglenceli, esprili, interaktif olsun."
     1) Kademeli GORULUR giris (38px / .9s / 130ms)
     2) Levha -> mermer tablet modal
     3) Levha egilmesi (3B tilt) + isik takibi
     4) Sayaclar
     5) Yuzen altin zerreler
     6) Hero parallax (fare + kaydirma)
     7) Zeus'un Tavsiyesi balonu (rastgele, esprili)
     8) Simsek imleci izi
     9) Sayfa gecisinde Olympos yukleme ekrani
   ===================================================================== */
(function () {
  "use strict";

  /* ---------- 1) kademeli giris ---------- */
  function giris() {
    var o = document.querySelectorAll(".rv");
    if (!o.length) return;
    if (!("IntersectionObserver" in window)) {
      for (var k = 0; k < o.length; k++) o[k].classList.add("in");
      return;
    }
    var g = new IntersectionObserver(function (gs) {
      gs.forEach(function (x) {
        if (!x.isIntersecting) return;
        var el = x.target, kap = el.parentElement || document.body;
        var kardes = Array.prototype.filter.call(kap.children, function (c) {
          return c.classList && c.classList.contains("rv");
        });
        var s = kardes.indexOf(el);
        el.style.transitionDelay = (s > 0 ? s * 130 : 0) + "ms";
        el.classList.add("in");
        g.unobserve(el);
      });
    }, { threshold: 0.12, rootMargin: "0px 0px -8% 0px" });
    for (var i = 0; i < o.length; i++) g.observe(o[i]);
    /* guvenlik agi: gozlemci bir sebeple tetiklenmezse icerik gizli kalmasin */
    setTimeout(function () {
      for (var k = 0; k < o.length; k++) {
        var e = o[k], r = e.getBoundingClientRect();
        if (!e.classList.contains("in") && r.top < window.innerHeight) e.classList.add("in");
      }
    }, 2200);
  }

  /* ---------- 2) modal ---------- */
  function modal() {
    var k = document.getElementById("olyModal");
    if (!k) return;
    var govde = k.querySelector(".govde"),
        baslik = k.querySelector("h3"),
        ust = k.querySelector(".ust");
    function ac(v) {
      baslik.textContent = v.baslik || "";
      ust.innerHTML = v.ust || "";
      govde.innerHTML = v.govde || "";
      k.classList.add("open");
      document.body.style.overflow = "hidden";
    }
    function kapat() { k.classList.remove("open"); document.body.style.overflow = ""; }
    document.addEventListener("click", function (e) {
      var t = e.target.closest ? e.target.closest("[data-modal]") : null;
      if (t) {
        ac({ baslik: t.getAttribute("data-baslik") || "",
             ust: t.getAttribute("data-ust") || "",
             govde: t.getAttribute("data-govde") || "" });
        return;
      }
      if (e.target.closest && e.target.closest("#olyModal .kapat")) kapat();
      if (e.target.classList && e.target.classList.contains("perde")) kapat();
    });
    document.addEventListener("keydown", function (e) { if (e.key === "Escape") kapat(); });
  }

  /* ---------- 3) levha egilmesi ---------- */
  function egil() {
    var levhalar = document.querySelectorAll(".levha");
    for (var i = 0; i < levhalar.length; i++) {
      (function (el) {
        el.addEventListener("mousemove", function (e) {
          var r = el.getBoundingClientRect();
          var x = (e.clientX - r.left) / r.width - .5;
          var y = (e.clientY - r.top) / r.height - .5;
          el.style.transform = "translateY(-7px) perspective(760px) rotateX(" +
            (-y * 6).toFixed(2) + "deg) rotateY(" + (x * 7).toFixed(2) + "deg)";
        });
        el.addEventListener("mouseleave", function () { el.style.transform = ""; });
      })(levhalar[i]);
    }
  }

  /* ---------- 4) sayac ---------- */
  function sayac() {
    var h = document.querySelectorAll("[data-say]");
    if (!h.length || !("IntersectionObserver" in window)) return;
    var g = new IntersectionObserver(function (gs) {
      gs.forEach(function (x) {
        if (!x.isIntersecting) return;
        var el = x.target, son = parseFloat(el.getAttribute("data-say")) || 0;
        var ek = el.getAttribute("data-son") || "", sure = 1200, bas = performance.now();
        (function adim(t) {
          var o = Math.min(1, (t - bas) / sure), e = 1 - Math.pow(1 - o, 3);
          el.textContent = Math.round(son * e).toLocaleString("tr-TR") + ek;
          if (o < 1) requestAnimationFrame(adim);
        })(performance.now());
        g.unobserve(el);
      });
    }, { threshold: 0.4 });
    for (var i = 0; i < h.length; i++) g.observe(h[i]);
  }

  /* ---------- 5) altin zerreler ---------- */
  function zerre() {
    var kap = document.querySelector(".oly-toz");
    if (!kap) return;
    for (var i = 0; i < 26; i++) {
      var z = document.createElement("i");
      z.style.left = (Math.random() * 100) + "%";
      z.style.animationDuration = (10 + Math.random() * 14) + "s";
      z.style.animationDelay = (-Math.random() * 20) + "s";
      var b = 2 + Math.random() * 4;
      z.style.width = z.style.height = b + "px";
      kap.appendChild(z);
    }
  }

  /* ---------- 6) hero parallax ---------- */
  function parallax() {
    var s = document.querySelector(".oly-hero .sahne");
    if (!s) return;
    var fx = 0, fy = 0;
    document.addEventListener("mousemove", function (e) {
      fx = (e.clientX / window.innerWidth - .5) * 22;
      fy = (e.clientY / window.innerHeight - .5) * 16;
      ciz();
    }, { passive: true });
    window.addEventListener("scroll", ciz, { passive: true });
    function ciz() {
      var k = window.scrollY * 0.16;
      s.style.transform = "scale(1.08) translate3d(" + (-fx) + "px," + (-fy + k) + "px,0)";
    }
    ciz();
  }

  /* ---------- 7) Zeus'un tavsiyesi ---------- */
  var TAVSIYE = [
    "Güç barına tıklayıp işaret koy, sonra <b>M</b> tuşuna bas. Aynı gücü tutturmak için uğraşma, Olympos senin yerine doldursun.",
    "Rüzgâr arkandan esiyorsa gücü bir tık kıs. Yoksa mermi bossu geçer, manzarayı vurursun.",
    "Keşif kilidini açmak istiyorsan aynı keşfe ısrarla gir. Zeus bile ilk denemede vuramadı.",
    "Kayıt eli alırken odaya <b>tek başına</b> gir. Yanında arkadaş varsa süre sayılmaz, kural kural.",
    "Tezgâhını kurmadan önce tezgâh alanının üzerinde bir an dur. Havada dükkân açılmıyor.",
    "Enerjin bitmişse kart açmak boşuna: sandık açılır ama içinden rüzgâr çıkar.",
    "Yüksek açı engel aşmak içindir. Düz atış hızlıdır ama duvarları sevmez.",
    "Otomatik ava bırakmadan önce el sayısını kontrol et. Enerji peşin gider, pişman olma."
  ];
  function zeus() {
    var el = document.querySelector(".zeus-tip");
    if (!el) return;
    var b = el.querySelector("span");
    function yeni() {
      b.innerHTML = TAVSIYE[Math.floor(Math.random() * TAVSIYE.length)];
    }
    yeni();
    setTimeout(function () { el.classList.add("gorun"); }, 2600);
    el.addEventListener("click", function () {
      el.classList.remove("gorun");
      setTimeout(function () { yeni(); el.classList.add("gorun"); }, 420);
    });
  }

  /* ---------- 8) simsek imleci izi ---------- */
  function simsek() {
    if (window.matchMedia("(pointer:coarse)").matches) return;
    var son = 0;
    document.addEventListener("mousemove", function (e) {
      var t = Date.now();
      if (t - son < 34) return;
      son = t;
      var p = document.createElement("span");
      p.style.cssText = "position:fixed;left:" + e.clientX + "px;top:" + e.clientY +
        "px;width:6px;height:6px;border-radius:50%;pointer-events:none;z-index:999;" +
        "background:#90ceee;box-shadow:0 0 12px #90ceee;opacity:.85;" +
        "transform:translate(-50%,-50%);transition:opacity .55s,transform .55s";
      document.body.appendChild(p);
      requestAnimationFrame(function () {
        p.style.opacity = "0";
        p.style.transform = "translate(-50%,-50%) scale(.2)";
      });
      setTimeout(function () { p.remove(); }, 600);
    }, { passive: true });
  }

  function basla() { giris(); modal(); egil(); sayac(); zerre(); parallax(); zeus(); simsek(); aktif(); }

  function aktif() {
    var yol = location.pathname.split("/").pop().toLowerCase();
    var b = document.querySelectorAll(".oly-baglar a");
    for (var i = 0; i < b.length; i++) {
      var h = (b[i].getAttribute("href") || "").split("/").pop().toLowerCase();
      if (h && h === yol) b[i].classList.add("on");
    }
  }

  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", basla);
  else basla();
})();

/* =====================================================================
   9) SAYFA GECISI YUKLEME EKRANI (tasarim 4 / 5 / 6)
   ---------------------------------------------------------------------
   Batuhan: "4-5-6 full site ici arayuzlerde gezerken anlik takilma
   olursa diye yukleme alani".
   Gorseldeki altin cubugun TAM ICINE canli dolgu bindiriyoruz. Konumlar
   piksel taramasiyla olculdu (_TASARIM/site_yukleme_hazirla.py):
     [sol%, ust%, genislik%, yukseklik%]
   ===================================================================== */
(function () {
  "use strict";

  var EKRAN = [
    { g: "assets/olympos/siteyuk4.jpg", bar: [14.531, 92.130, 72.083, 1.759] },
    { g: "assets/olympos/siteyuk5.jpg", bar: [13.802, 93.333, 73.073, 2.685] },
    { g: "assets/olympos/siteyuk6.jpg", bar: [10.833, 92.315, 76.406, 2.130] }
  ];
  var DURUM = ["Olympos hazırlanıyor", "Mermer basamaklar diziliyor",
               "Rüzgâr ölçülüyor", "Kapılar açılıyor"];

  var kat = null, sayac = null;

  function kur() {
    var d = document.createElement("div");
    d.className = "oly-load"; d.id = "olyLoad";
    d.innerHTML = '<div class="tuval">' +
      '<div class="satir"><b></b><span>0%</span></div>' +
      '<div class="cubuk"><i></i></div></div>';
    document.body.appendChild(d);
    return d;
  }

  function yerlestir(ekran) {
    var t = kat.querySelector(".tuval"),
        c = kat.querySelector(".cubuk"),
        r = kat.querySelector(".satir"),
        b = ekran.bar;
    t.style.backgroundImage = "url('" + ekran.g + "')";
    c.style.left = b[0] + "%"; c.style.top = b[1] + "%";
    c.style.width = b[2] + "%"; c.style.height = b[3] + "%";
    r.style.left = b[0] + "%"; r.style.width = b[2] + "%";
    r.style.top = (b[1] - 4.4) + "%";
  }

  function goster(sonra) {
    if (!kat) kat = document.getElementById("olyLoad") || kur();
    yerlestir(EKRAN[Math.floor(Math.random() * EKRAN.length)]);
    kat.classList.add("on");

    var dolgu = kat.querySelector(".cubuk i"),
        yuzde = kat.querySelector(".satir span"),
        yazi  = kat.querySelector(".satir b"),
        o = 8;

    function ciz() {
      dolgu.style.width = o + "%";
      yuzde.textContent = Math.round(o) + "%";
      yazi.textContent = DURUM[Math.min(DURUM.length - 1, Math.floor(o / 26))];
    }
    ciz();
    clearInterval(sayac);
    sayac = setInterval(function () {
      o += Math.random() * 15 + 6;
      if (o >= 95) { o = 95; clearInterval(sayac); }
      ciz();
    }, 95);

    if (typeof sonra === "function") setTimeout(sonra, 360);
  }

  function gizle() {
    if (!kat) return;
    clearInterval(sayac);
    var d = kat.querySelector(".cubuk i"), y = kat.querySelector(".satir span");
    if (d) d.style.width = "100%";
    if (y) y.textContent = "100%";
    setTimeout(function () { kat.classList.remove("on"); }, 260);
  }

  document.addEventListener("click", function (e) {
    var a = e.target.closest ? e.target.closest("a") : null;
    if (!a) return;
    var h = a.getAttribute("href") || "";
    if (!h || h.charAt(0) === "#" || a.target === "_blank") return;
    if (/^(https?:)?\/\//i.test(h) && h.indexOf(location.host) === -1) return;
    if (/^(mailto:|tel:|javascript:)/i.test(h)) return;
    e.preventDefault();
    goster(function () { location.href = h; });
  });
  window.addEventListener("pageshow", gizle);
  if (document.readyState === "complete") gizle(); else window.addEventListener("load", gizle);
  window.olympos = { goster: goster, gizle: gizle };
})();
