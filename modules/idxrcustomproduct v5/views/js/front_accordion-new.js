"use strict";
document.addEventListener("DOMContentLoaded", function () {
  const t = (() => {
    var t,
      e,
      o = {
        on: !1,
        scoleColor: "#000000",
        faces: 5,
        socleHeight: 0,
        vitrineWidth: 0,
        vitrineHeight: 0,
        vitrineDepth: 0,
        socleThikness: 10,
        base: !0,
      },
      n = { type: 1, x: 0, y: 0, start: 1 },
      a = { type: 0, width: 0, height: 0 },
      i = { type: 0, width: 0, height: 0 },
      r = { width: 0, height: 0 };
    const c = 20,
      s = 15;
    var l = 1,
      d = 0,
      _ = 0,
      u = 0,
      p = 1,
      m = 0;
    const x = [
        { id: "text_3", default: 200 },
        { id: "text_4", default: 100 },
        { id: "text_109", default: 0 },
        { id: "text_110", default: 0 },
        { id: "text_111", default: 0 },
        { id: "text_112", default: 0 },
        { id: "text_9", default: 200 },
        { id: "text_10", default: 150 },
        { id: "text_11", default: 200 },
        { id: "text_13", default: 5 },
        { id: "text_57", default: 100 },
        { id: "text_58", default: 40 },
        { id: "text_53", default: 100 },
        { id: "text_54", default: 100 },
        { id: "text_55", default: 40 },
        { id: "text_56", default: 40 },
        { id: "text_83", default: 200 },
        { id: "text_82", default: 200 },
      ],
      h = [
        {
          id: 1,
          fields: [
            "text_3",
            "text_4",
            "text_109",
            "text_110",
            "text_111",
            "text_112",
          ],
        },
        { id: 2, fields: ["text_9"] },
        { id: 3, fields: ["text_9"] },
        { id: 4, fields: ["text_3", "text_4"] },
        { id: 5, fields: ["text_3", "text_4", "text_10"] },
        { id: 6, fields: ["text_3", "text_4", "text_10"] },
        { id: 7, fields: ["text_3", "text_10"] },
        { id: 8, fields: ["text_10", "text_11"] },
        { id: 9, fields: ["text_10", "text_11"] },
        { id: 10, fields: ["text_10", "text_11"] },
        { id: 11, fields: ["text_9"] },
        { id: 12, fields: ["text_53", "text_54", "text_55", "text_56"] },
        { id: 13, fields: ["text_13", "text_57", "text_58"] },
        { id: 14, fields: ["text_6", "text_81", "text_82", "text_83"] },
        { id: 15, fields: ["text_71", "text_70"] },
      ],
      f = [
        "text_3",
        "text_4",
        "text_109",
        "text_110",
        "text_111",
        "text_112",
        "text_9",
        "text_10",
        "text_11",
        "text_53",
        "text_54",
        "text_55",
        "text_56",
        "text_13",
        "text_57",
        "text_58",
        "text_6",
        "text_81",
        "text_82",
        "text_83",
        "text_71",
        "text_70",
      ],
      v = [
        { id: 1, fields: ["text_18", "text_21", "text_22", "text_23"] },
        {
          id: 2,
          fields: ["text_18", "text_25", "text_26", "text_27", "text_28"],
        },
        { id: 3, fields: ["text_18", "text_21", "text_22", "text_23"] },
      ],
      g = [
        "text_18",
        "text_25",
        "text_26",
        "text_27",
        "text_28",
        "text_21",
        "text_22",
        "text_23",
      ],
      k = [
        {
          id: 1,
          fields: [
            "text_38",
            "text_39",
            "text_40",
            "text_41",
            "text_42",
            "text_113",
            "text_114",
            "text_115",
            "text_116",
          ],
        },
        { id: 2, fields: ["text_40", "text_41", "text_45"] },
        { id: 3, fields: ["text_40", "text_41", "text_45", "text_42"] },
        {
          id: 4,
          fields: ["text_38", "text_39", "text_40", "text_41", "text_42"],
        },
        {
          id: 5,
          fields: [
            "text_38",
            "text_39",
            "text_40",
            "text_41",
            "text_42",
            "text_63",
          ],
        },
        {
          id: 6,
          fields: [
            "text_38",
            "text_39",
            "text_40",
            "text_41",
            "text_42",
            "text_63",
          ],
        },
        {
          id: 7,
          fields: ["text_38", "text_39", "text_40", "text_41", "text_42"],
        },
        {
          id: 8,
          fields: ["text_38", "text_39", "text_40", "text_41", "text_42"],
        },
        {
          id: 9,
          fields: ["text_38", "text_39", "text_40", "text_41", "text_42"],
        },
        {
          id: 10,
          fields: ["text_38", "text_39", "text_40", "text_41", "text_42"],
        },
        { id: 11, fields: ["text_40", "text_41", "text_45", "text_42"] },
        {
          id: 12,
          fields: [
            "text_40",
            "text_41",
            "text_42",
            "text_66",
            "text_67",
            "text_68",
            "text_69",
          ],
        },
        {
          id: 13,
          fields: ["text_40", "text_41", "text_42", "text_64", "text_65"],
        },
      ],
      M = [
        "text_38",
        "text_39",
        "text_40",
        "text_41",
        "text_42",
        "text_113",
        "text_114",
        "text_115",
        "text_116",
        "text_45",
        "text_63",
        "text_64",
        "text_65",
        "text_66",
        "text_67",
        "text_68",
        "text_69",
        "text_64",
        "text_65",
      ];
    function b(o = 0) {
      1 === o
        ? ((i.type = 0), (a.type = 0), I(17), I(29), E(), j())
        : 2 === o && (E(), j()),
        (function () {
          (t = Snap("#actualSvg")), (e = t.select("#shapeContainer"));
          const o = t.select("#arrowsContainer"),
            d = t.select("#holesContainer"),
            _ = t.select("#cutoutContainer"),
            u = t.select("#cutoutDems");
          e.clear(), o.clear(), d.clear(), _.clear(), u.clear();
          var x = e,
            h = o;
          function f() {
            let t = $("#svgContainer #actualSvg").clone();
            $("#step_2_preview, #step_17_preview, #step_29_preview")
              .empty()
              .append(t.clone());
          }
          var v = y(n.type);
          function g(t, e = 400) {
            const o = Math.max(...t);
            return 0 == o ? 1 : e / o;
          }
          function k(t = 1) {
            return 2 == t
              ? { fill: "#e2ffc1", stroke: "#065075" }
              : { fill: "#F0FAFF", stroke: "#065075", id: "shapeHolder" };
          }
          function M(t) {
            return parseFloat(t) * l;
          }
          function b(t) {
            if (0 === l) throw new Error("scaleFactor cannot be zero");
            const e = parseFloat(t) / l;
            return parseFloat(e.toFixed(2));
          }
          function y(t) {
            var e = 0;
            switch (t) {
              case 1:
                var o = X("text_3", 1200),
                  n = X("text_4", 1200),
                  a = X("text_109", 10),
                  i = X("text_110", 15),
                  c = X("text_111", 20),
                  s = X("text_112", 1);
                (l = g([o, n])),
                  L(o, n, a, i, c, s),
                  w(o, n, 0, "Rectangle"),
                  (p = 2 * (o + n));
                break;
              case 2:
                var d = X("text_9", 200);
                (l = g([d])), B(d), w(d, d, 0, "Cercle"), (p = Math.PI * d);
                break;
              case 3:
                d = X("text_9", 40);
                (l = g([d])),
                  F(d),
                  w(d, d / 2, 0, "Demi cercle"),
                  (p = (Math.PI * d) / 2 + d);
                break;
              case 4:
                (o = X("text_3", 150)), (n = X("text_4", 100));
                (l = g([o, n])), S(o, n), w(o, n, 0, "Cercle");
                let v = o / 2,
                  k = n / 2;
                p =
                  Math.PI *
                  (3 * (v + k) - Math.sqrt((3 * v + k) * (v + 3 * k)));
                break;
              case 5:
                (o = X("text_3", 150)), (n = X("text_4", 300));
                var _ = X("text_10", 200);
                (l = g([o, n, _])),
                  D(o, n, _),
                  (p =
                    o +
                    n +
                    2 * Math.sqrt(Math.pow(_, 2) + Math.pow((o - n) / 2, 2))),
                  w(Math.max(o, _), n, 0, "Trapézoïdale droite");
                break;
              case 6:
                (o = X("text_3", 200)),
                  (n = X("text_4", 250)),
                  (_ = X("text_10", 300));
                (l = g([n, Math.max(o, _)])),
                  E(o, _, n),
                  (p =
                    o +
                    n +
                    2 * Math.sqrt(Math.pow(_, 2) + Math.pow((o - n) / 2, 2))),
                  w(Math.max(o, _), n, 0, "Trapézoïdale droite");
                break;
              case 7:
                (o = X("text_3", 160)), (n = X("text_10", 200));
                (l = g([o, n])), j(o, n), w(o, n, 0, "Rectangle à arc");
                var u = o / 2;
                p = 2 * (o + (n - u)) + Math.PI * u;
                break;
              case 8:
              case 9:
              case 10:
                o = X("text_10", 260);
                var m = X("text_11", 200);
                (l = g([o, m])),
                  q(m, o, 10 === t ? 1 : 9 === t ? 0 : void 0),
                  (p = m + o + Math.sqrt(m * m + o * o)),
                  w(m, o, 0, "Triangle");
                break;
              case 11:
                o = X("text_9", 150);
                (l = g([o], 200)),
                  H(o),
                  w(2 * o, o * Math.sqrt(3), 0, "Hexagone"),
                  (p = 6 * o),
                  (e = o);
                break;
              case 12:
                let M = X("text_53", 200),
                  b = X("text_54", 200),
                  y = X("text_55", 220),
                  P = X("text_56", 120);
                (l = g([M + b, y + 2 * P])),
                  T(M, b, y, P),
                  w(M + b, y + 2 * P, 0, "Flèche");
                let I = 2 * (M + y),
                  N = b + P + Math.sqrt(b * b + P * P);
                p = I + N - y;
                break;
              case 13:
                let O = X("text_57", 100),
                  W = X("text_58", 40),
                  V = X("text_13", 5);
                (l = g([O, W])), R(O, W, V), w(2 * O, 2 * O, 0, "Étoile");
                let Z = Math.PI / V,
                  G = Math.sqrt(
                    Math.pow(O, 2) + Math.pow(W, 2) - 2 * O * W * Math.cos(Z)
                  );
                p = 2 * G * V;
                break;
              case 14:
                var x = $("#text_6").val() || "Aziz",
                  h =
                    $("#text_81").val() ||
                    "/modules/idxrcustomproduct/views/js/fonts/Alfa_Slab_One/AlfaSlabOne-Regular.ttf",
                  f = X("text_83", 200);
                (l = g([f])), z(x, h, f);
                break;
              case 15:
                (o = r.width), (n = r.height);
                (l = g([o, n])),
                  C(o, n, 0),
                  w(o, n, 0, "Plaques pré-découpées"),
                  (p = 2 * (o + n));
                break;
              case 16:
                (o = X("text_3", 100)), (n = X("text_4", 200));
                (l = g([o, n])), A(o, n), w(o, n, 0, "Cercle");
                let Y = o / 2,
                  K = n / 2;
                p =
                  Math.PI *
                  (3 * (Y + K) - Math.sqrt((3 * Y + K) * (Y + 3 * K)));
            }
            return e;
          }
          function P(t = 0, e = 0) {
            var o = n.type;
            const i = { class: "hole", fill: "#FFFFFF", stroke: "#065075" };
            var r = 0;
            switch ((12 == n.type && (r = M(X("text_56", 120))), t)) {
              case 0:
                break;
              case 1:
                var c = X("text_21", 5),
                  s = X("text_22", 5),
                  l = M(X("text_18", 5)),
                  _ = M(X("text_23", 15));
                8 === o || 9 === o || 10 === o
                  ? g(c, s, _, l, r, o)
                  : 11 === o
                  ? k(c, s, _, l, r, e)
                  : h(c, s, _, l, r);
                break;
              case 2:
                var u = X("text_28", 8),
                  p = M(X("text_18", 10)),
                  m = M(X("text_27", 25));
                f(M(X("text_25", 40)), M(X("text_26", 40)), m, u, p, r);
                break;
              case 3:
                (c = X("text_21", 5)),
                  (s = X("text_22", 6)),
                  (l = M(X("text_18", 5)));
                x(c, s, (_ = M(X("text_23", 10))), l, r);
                break;
              case 4:
                v(a.width, a.height, r);
                break;
              default:
                console.error("Invalid hole type");
            }
            function x(t, e, o, n, r) {
              const c = (M(a.width) - 2 * o) / (e - 1),
                s = (M(a.height) - 2 * o) / (t - 1);
              for (let a = 0; a < t; a++)
                for (let t = 0; t < e; t++) {
                  const e = o + c * t,
                    _ = o + s * a - r;
                  0 === t && 0 === a && Z(e, o - r, l, r, 0, o),
                    d.circle(e, _, n).attr(i);
                }
            }
            function h(t, e, o, n, r) {
              if (1 === t && 1 === e) {
                const t = o,
                  e = o - r;
                return void d.circle(t, e, n).attr(i);
              }
              if (1 === t || 1 === e) {
                if (1 === t) {
                  const t = (M(a.width) - 2 * o) / (e - 1);
                  for (let a = 0; a < e; a++) {
                    const e = o + t * a;
                    d.circle(e, o - r, n).attr(i);
                  }
                }
                if (1 === e) {
                  const e = (M(a.height) - 2 * o) / (t - 1);
                  for (let a = 0; a < t; a++) {
                    const t = o + e * a - r;
                    d.circle(o, t, n).attr(i);
                  }
                }
                return;
              }
              const c = (M(a.width) - 2 * o) / (e - 1),
                s = (M(a.height) - 2 * o) / (t - 1);
              for (let t = 0; t < e; t++) {
                const e = o + c * t;
                0 === t && Z(e, o - r, n, r, 0, o),
                  d.circle(e, o - r, n).attr(i),
                  d.circle(e, M(a.height) - o - r, n).attr(i);
              }
              for (let e = 1; e < t - 1; e++) {
                const t = o + s * e;
                d.circle(o, t - r, n).attr(i),
                  d.circle(M(a.width) - o, t - r, n).attr(i);
              }
            }
            function f(t, e, o, n, a, r) {
              if (1 === n) return void d.circle(t, e - r, a).attr(i);
              const c = (2 * Math.PI) / n;
              for (let s = 0; s < n; s++) {
                const n = s * c,
                  l = t + o * Math.cos(n),
                  _ = e + o * Math.sin(n) - r;
                0 === s && Z(l, _, a, r, o, 0, t, e), d.circle(l, _, a).attr(i);
              }
            }
            function v(t, e, o = t / 2, n = e / 2, a) {
              d.circle(o, n, 5).attr(i);
            }
            function $(t, e, o) {
              const [n, a] = t,
                [i, r] = e,
                [c, s] = o,
                l = [n - i, a - r],
                d = [c - i, s - r],
                _ =
                  (l[0] * d[0] + l[1] * d[1]) /
                  (Math.sqrt(l[0] ** 2 + l[1] ** 2) *
                    Math.sqrt(d[0] ** 2 + d[1] ** 2));
              return Math.acos(_);
            }
            function g(t, e, o, n, r, c = 8) {
              var s = M(a.height),
                _ = M(a.width),
                u = [_ / 2, s / 2],
                p = _ / 2;
              9 === c
                ? ((p = o), (u = [0, 0]))
                : 10 === c && ((u = [_, 0]), (p = _ - o));
              var m = $([_, s], u, [0, s]),
                x = o;
              Math.abs(Math.sin(m / 2)) > Number.EPSILON &&
                (x = Math.abs(o / Math.sin(m / 2)));
              var h = $(u, [0, s], [_, s]),
                f = o,
                v = 8 === c ? h : h / 2;
              Math.abs(Math.sin(v)) > Number.EPSILON &&
                9 !== c &&
                (f = Math.abs(o / Math.sin(v)));
              var g = $(u, [_, s], [0, s]),
                k = o,
                b = 8 === c ? g : g / 2;
              Math.abs(Math.sin(b)) > Number.EPSILON &&
                10 !== c &&
                (k = Math.abs(o / Math.sin(b)));
              const w = x,
                y = f,
                P = s - o,
                I = _ - k,
                C = s - o;
              for (let t = 0; t < e; t++) {
                const a = p - (t / (e - 1)) * (p - y),
                  c = w + (t / (e - 1)) * (P - w);
                0 === t && Z(a, c - r, l, r, 0, o),
                  d.circle(a, c - r, n).attr(i);
              }
              for (let e = 0; e < t; e++) {
                const o = y + (e / (t - 1)) * (I - y),
                  a = P + (e / (t - 1)) * (C - P);
                d.circle(o, a - r, n).attr(i);
              }
              for (let t = 0; t < e; t++) {
                const o = p + (t / (e - 1)) * (I - p),
                  a = w + (t / (e - 1)) * (C - w);
                d.circle(o, a - r, n).attr(i);
              }
            }
            function k(t, e, o, n, a, r = 0, c = 1, s = 0, _ = 0) {
              const u = M(r),
                p = u * Math.sqrt(3);
              let m = s,
                x = _;
              2 === c && ((m = s - u), (x = _ - p / 2));
              const h = [
                [m + u / 2 + o, x + o],
                [m + 1.5 * u - o, x + o],
                [m + 2 * u - o, x + p / 2],
                [m + 1.5 * u - o, x + p - o],
                [m + u / 2 + o, x + p - o],
                [m + o, x + p / 2],
              ];
              var f = 0;
              function v(t, e, r) {
                for (let c = 0; c < r; c++) {
                  const s = r > 1 ? c / (r - 1) : 1,
                    _ = t[0] + (e[0] - t[0]) * s,
                    u = t[1] + (e[1] - t[1]) * s;
                  d.circle(_, u - a, n).attr(i),
                    0 === c && 0 === f && (Z(_, o - a, l, a, 0, o), (f = 1));
                }
              }
              for (let o = 0; o < 6; o++) {
                v(h[o], h[(o + 1) % 6], o % 3 == 0 ? e : t);
              }
            }
          }
          function I(e = 12) {
            (x = _), (h = u);
            var a = 0;
            12 == n.type && (a = M(X("text_56", 80)));
            var i = X("text_40", 200),
              r = X("text_41", 500),
              c = M(i),
              s = M(r) - a,
              l = !0;
            switch (e) {
              case 1:
                var d = X("text_113", 0),
                  p = X("text_114", 0),
                  f = X("text_115", 0),
                  v = X("text_116", 0);
                L(
                  (b = X("text_38", 700)),
                  (g = X("text_39", 500)),
                  d,
                  p,
                  f,
                  v,
                  2,
                  c,
                  s
                ),
                  (m = 2 * (b + g));
                break;
              case 2:
                B((w = X("text_45", 30)), 2, c, s), (m = Math.PI * w);
                break;
              case 3:
                F((w = X("text_45", 50)), 2, c, s), (m = (Math.PI * w) / 2 + w);
                break;
              case 4:
                S((b = X("text_38", 20)), (g = X("text_39", 40)), 2, c, s);
                let t = b / 2,
                  o = g / 2;
                m =
                  Math.PI *
                  (3 * (t + o) - Math.sqrt((3 * t + o) * (t + 3 * o)));
                break;
              case 5:
                var $ = X("text_63", 30),
                  g = X("text_38", 20);
                D($, (b = X("text_39", 40)), g, 2, c, s),
                  (m =
                    $ +
                    g +
                    2 * Math.sqrt(Math.pow(b, 2) + Math.pow(($ - g) / 2, 2)));
                break;
              case 6:
                ($ = X("text_63", 30)), (g = X("text_38", 20));
                E($, (b = X("text_39", 40)), g, 2, c, s),
                  (m =
                    $ +
                    g +
                    2 * Math.sqrt(Math.pow(b, 2) + Math.pow(($ - g) / 2, 2)));
                break;
              case 7:
                j((b = X("text_38", 20)), (g = X("text_39", 40)), 2, c, s);
                var k = b / 2;
                m = 2 * (b + (g - k)) + Math.PI * k;
                break;
              case 8:
              case 9:
              case 10:
                var b;
                q(
                  (b = X("text_38", 20)),
                  (g = X("text_39", 40)),
                  10 === e ? 1 : 9 === e ? 0 : void 0,
                  2,
                  c,
                  s
                ),
                  (m = g + b + Math.sqrt(g * g + b * b));
                break;
              case 11:
                var w;
                H((w = X("text_45", 30)), 2, c, s), (m = 6 * w);
                break;
              case 12:
                var y = X("text_66", 50),
                  P = X("text_67", 20),
                  I = X("text_68", 20),
                  C = X("text_69", 20);
                T(y, P, I, C, 2, c, s);
                let n = 2 * (y + I),
                  a = P + C + Math.sqrt(P * P + C * C);
                m = n + a - I;
                break;
              case 13:
                var A = 5,
                  z = X("text_64", 50),
                  N = X("text_65", 20);
                R(z, N, 5, 2, c, s);
                let i = Math.PI / A,
                  r = Math.sqrt(
                    Math.pow(z, 2) + Math.pow(N, 2) - 2 * z * N * Math.cos(i)
                  );
                m = 2 * r * A;
                break;
              default:
                (l = !1), (m = 0);
            }
            var O = X("text_42", 0),
              V = t.select("#couOutMain");
            V && V.transform(`r${O},${c},${s}`),
              l &&
                ((h = o),
                W(0, s, c, s, "X: ", `${i.toFixed(2)} mm`, "", 2),
                W(c, 0, c, s, "Y: ", `${r.toFixed(2)} mm`, "vertical", 2));
          }
          function C(t, e, o, n = 1, a = 0, i = 0) {
            const r = M(t),
              s = M(e),
              l = M(o);
            let d = a,
              _ = i;
            2 === n && ((d = a - r / 2), (_ = i - s / 2)),
              x.rect(d, _, r, s, o).attr(k(n));
            var u = c;
            if (
              (1 === n ? Y(r, s) : 2 === n && (u -= 10),
              W(d, _ + s + u, d + r, _ + s + u, "Largeur: ", `${t} mm`, "", n),
              W(d - u, _, d - u, _ + s, "Hauteur: ", `${e} mm`, "vertical", n),
              o > 1)
            ) {
              const t = d + r - l,
                e = _ + l,
                a = d + r - l,
                i = _ - 10;
              x
                .line(t, e, t + l + 10, e)
                .attr({
                  stroke: "red",
                  "stroke-width": 1,
                  "stroke-dasharray": "2, 2",
                }),
                x
                  .line(a, i, a, _ + l)
                  .attr({
                    stroke: "red",
                    "stroke-width": 1,
                    "stroke-dasharray": "2, 2",
                  }),
                W(t, i, t + l + 10, i, "Rayon: ", `${o} mm`, "horizontal", n);
            }
          }
          function L(t, e, o, n, a, i, r = 1, s = 0, l = 0) {
            const d = M(t),
              _ = M(e),
              u = M(o),
              p = M(n),
              m = M(a),
              h = M(i);
            let f = s,
              v = l;
            2 === r && ((f = s - d / 2), (v = l - _ / 2));
            var $ = [
              "M",
              f + u,
              v,
              "H",
              f + d - p,
              "A",
              p,
              p,
              0,
              0,
              1,
              f + d,
              v + p,
              "V",
              v + _ - h,
              "A",
              h,
              h,
              0,
              0,
              1,
              f + d - h,
              v + _,
              "H",
              f + m,
              "A",
              m,
              m,
              0,
              0,
              1,
              f,
              v + _ - m,
              "V",
              v + u,
              "A",
              u,
              u,
              0,
              0,
              1,
              f + u,
              v,
              "Z",
            ].join(" ");
            x.path($).attr(k(r));
            var g = c;
            if (
              (1 === r ? Y(d, _) : 2 === r && (g -= 10),
              W(f, v + _ + g, f + d, v + _ + g, "Largeur: ", `${t} mm`, "", r),
              W(f - g, v, f - g, v + _, "Hauteur: ", `${e} mm`, "vertical", r),
              u > 1)
            ) {
              const t = f + u,
                e = v + u;
              x
                .line(t, e, t - u - 10, e)
                .attr({
                  stroke: "green",
                  "stroke-width": 1,
                  "stroke-dasharray": "2, 2",
                }),
                x
                  .line(t, v, t, e)
                  .attr({
                    stroke: "green",
                    "stroke-width": 1,
                    "stroke-dasharray": "2, 2",
                  }),
                W(t - u - 10, e, t, e, "Rayon: ", `${o} mm`, "horizontal", 2);
            }
            if (p > 1) {
              const t = f + d - p,
                e = v + p;
              x
                .line(t, e, t + p + 10, e)
                .attr({
                  stroke: "red",
                  "stroke-width": 1,
                  "stroke-dasharray": "2, 2",
                }),
                x
                  .line(t, v, t, e)
                  .attr({
                    stroke: "red",
                    "stroke-width": 1,
                    "stroke-dasharray": "2, 2",
                  }),
                W(t + p + 10, e, t, e, "Rayon: ", `${n} mm`, "horizontal", 2);
            }
            if (m > 1) {
              const t = f + m,
                e = v + _ - m;
              x
                .line(t, e, t - m - 10, e)
                .attr({
                  stroke: "red",
                  "stroke-width": 1,
                  "stroke-dasharray": "2, 2",
                }),
                x
                  .line(t, v + _, t, e)
                  .attr({
                    stroke: "red",
                    "stroke-width": 1,
                    "stroke-dasharray": "2, 2",
                  }),
                W(t - m - 10, e, t, e, "Rayon: ", `${a} mm`, "horizontal", 2);
            }
            if (h > 1) {
              const t = f + d - h,
                e = v + _ - h;
              x
                .line(t, e, t + h + 10, e)
                .attr({
                  stroke: "red",
                  "stroke-width": 1,
                  "stroke-dasharray": "2, 2",
                }),
                x
                  .line(t, v + _, t, e)
                  .attr({
                    stroke: "red",
                    "stroke-width": 1,
                    "stroke-dasharray": "2, 2",
                  }),
                W(t + h + 10, e, t, e, "Rayon: ", `${i} mm`, "horizontal", 2);
            }
          }
          function B(t, e = 1, o = 0, n = 0) {
            const a = M(t / 2);
            let i = o,
              r = n;
            2 === e ? ((i = o), (r = n)) : ((i = o + a), (r = n + a)),
              x.circle(i, r, a).attr(k(e)),
              1 === e && Y(2 * a, 2 * a),
              W(
                i - a,
                r + a + s,
                i + a,
                r + a + s,
                "Diamètre: ",
                `${t} mm`,
                "",
                e
              );
          }
          function F(t, e = 1, o = 0, n = 0) {
            const a = M(t / 2);
            let i = o,
              r = n;
            2 === e && ((i = o - a), (r = n - a));
            const c = `\n                    M ${i},${
              r + a
            } \n                    A ${a},${a} 0 0,1 ${i + 2 * a},${
              r + a
            }\n                `;
            x.path(c).attr(k(e)),
              1 === e && Y(2 * a, a),
              W(i, r + a, i + 2 * a, r + a, "Diamètre: ", `${t} mm`, "", e);
          }
          function S(t, e, o = 1, n = 0, a = 0) {
            const i = M(t / 2),
              r = M(e / 2),
              s = 2 * i,
              l = 2 * r;
            let d = n,
              _ = a;
            2 === o ? ((d = n), (_ = a)) : ((d = n + i), (_ = a + r)),
              x.ellipse(d, _, i, r).attr(k(o));
            var u = c;
            1 === o ? Y(s, l) : 2 === o && (u -= 10),
              W(
                d - i,
                _ + r + u,
                d + i,
                _ + r + u,
                "Largeur: ",
                `${t} mm`,
                "",
                o
              ),
              W(
                d - i - u,
                _ - r,
                d - i - u,
                _ + r,
                "Hauteur: ",
                `${e} mm`,
                "vertical",
                o
              );
          }
          function D(t, e, o, n = 1, a = 0, i = 0) {
            const r = M(t),
              s = M(o),
              l = M(e);
            let d = a,
              _ = i;
            2 === n && ((d = a - Math.max(r, s) / 2), (_ = i - l / 2));
            const u = d,
              p = _ + l,
              m = d + r,
              h = d + s,
              f = _,
              v = `\n                    M ${u},${p} \n                    L ${m},${
                _ + l
              } \n                    L ${h},${f} \n                    L ${u},${f} \n                    Z\n                `;
            x.path(v).attr(k(n));
            var $ = c;
            if (1 === n) {
              Y(Math.max(r, s), l);
            } else 2 === n && ($ -= 10);
            W(u, p + $, m, p + $, "Largeur: ", `${t} mm`, "", n),
              W(u, f - $, h, f - $, "Longueur: ", `${o} mm`, "horizontal", n),
              W(u - $, f, u - $, p, "Hauteur: ", `${e} mm`, "vertical", n);
          }
          function E(t, e, o, n = 1, a = 0, i = 0) {
            const r = M(t),
              s = M(e),
              l = M(o),
              d = (r - s) / 2;
            let _ = a + (r - s > 0 ? 0 : Math.abs(d)),
              u = i;
            2 === n && ((_ = a - r / 2 + d), (u = i - l / 2));
            const p = _,
              m = u + l,
              h = _ + r,
              f = _ + d + s,
              v = u,
              $ = _ + d,
              g = `\n                    M ${p},${m} \n                    L ${h},${
                u + l
              } \n                    L ${f},${v} \n                    L ${$},${v} \n                    Z\n                `;
            x.path(g).attr(k(n));
            var b = c;
            if (1 === n) {
              Y(Math.max(r, s), l);
            } else 2 === n && (b -= 10);
            W(p, m + b, h, m + b, "Largeur: ", `${t} mm`, "", n),
              W($, v - b, f, v - b, "Longueur: ", `${e} mm`, "horizontal", n),
              W(
                p - _ - b,
                v,
                p - _ - b,
                m,
                "Hauteur: ",
                `${o} mm`,
                "vertical",
                n
              );
          }
          function j(t, e, o = 1, n = 0, a = 0) {
            const i = M(t),
              r = M(e),
              s = i / 2;
            let l = n,
              d = a;
            2 === o && ((l = n - i / 2), (d = a - r / 2));
            var _ = `\n                    M${l},${
              d + r
            } \n                    v${
              -r + s
            } \n                    a${s},${s} 0 0,1 ${i},0 \n                    v${
              r - s
            } \n                    l ${-i},0\n                `;
            x.path(_).attr(k(o));
            const u = `\n                    M${l},${
              d + r - r + s
            } \n                    H${l + i}\n                `;
            x.path(u).attr({
              stroke: "#065075",
              fill: "none",
              "stroke-width": 1,
              "stroke-dasharray": "4, 4",
            });
            var p = c;
            1 === o ? Y(i, r) : 2 === o && (p -= 10),
              W(l, d + r + p, l + i, d + r + p, "Largeur: ", `${t} mm`, "", o),
              W(l - p, d, l - p, d + r, "Longueur: ", `${e} mm`, "vertical", o);
            const m = l + i / 2;
            W(
              m,
              d + s,
              m,
              d + r,
              "Hauteur: ",
              e - t / 2 + " mm",
              "vertical",
              o
            );
          }
          function q(t, e, o = 2, n = 1, a = 0, i = 0) {
            const r = M(t),
              s = M(e);
            let l = 0;
            1 === o ? (l = r) : 2 === o && (l = r / 2);
            let d = a,
              _ = i;
            2 === n && ((d = a - r / 2), (_ = i - s / 2));
            var u = `\n                    M${d},${
              _ + s
            } \n                    L${d + l},${_} \n                    L${
              d + r
            },${_ + s} \n                    Z\n                `;
            x.path(u).attr(k(n));
            var p = c;
            1 === n ? Y(r, s) : 2 === n && (p -= 10),
              W(d, _ + s + p, d + r, _ + s + p, "La base: ", `${t} mm`, "", n),
              W(d - p, _, d - p, _ + s, "Longueur: ", `${e} mm`, "vertical", n);
          }
          function H(t, e = 1, o = 0, n = 0) {
            const a = M(t),
              i = a * Math.sqrt(3);
            let r = o,
              s = n;
            2 === e && ((r = o - a), (s = n - i / 2));
            const l = [
              [r + a / 2, s],
              [r + 1.5 * a, s],
              [r + 2 * a, s + i / 2],
              [r + 1.5 * a, s + i],
              [r + a / 2, s + i],
              [r, s + i / 2],
            ]
              .map((t) => t.join(","))
              .join(" ");
            x.polygon(l).attr(k(e));
            var d = c;
            1 === e ? Y(2 * a, i) : 2 === e && (d -= 10),
              W(
                r + a / 2,
                s + i + d,
                r + 1.5 * a,
                s + i + d,
                "Côté: ",
                `${t} mm`,
                "",
                e
              ),
              W(r, s - d, r + 2 * a, s - d, "Largeur: ", 2 * t + " mm", "", e),
              W(
                r - d,
                s,
                r - d,
                s + i,
                "Hauteur: ",
                `${i.toFixed(2)} mm`,
                "vertical",
                e
              );
          }
          function T(e, n, a, i, r = 1, s = 0, l = 0) {
            var d = M(e),
              _ = M(n),
              u = M(a),
              p = M(i);
            const m = s + d,
              h = m + _;
            o.circle(s, l, 2);
            const f = `\n                    M ${s},${
              l + u
            }         \n                    L ${s},${l}                 \n                    L ${m},${l}           \n                    L ${m},${
              l - p
            }      \n                    L ${m + _},${
              l + u / 2
            }  \n                    L ${m},${
              l + u + p
            } \n                    L ${m},${
              l + u
            }      \n                    Z                             \n                `;
            1 === r &&
              t.attr({
                viewBox: `${s - 50} ${l - p - 50} ${h - s + 100} ${
                  u + p + 100
                }`,
              }),
              x.path(f).attr(k(r));
            var v = c;
            2 === r && (v -= 10),
              W(s, l - 10, m, l - 10, "L.Q: ", `${e} mm`, "", r),
              W(m, l - p - 10, h, l - p - 10, "L.T: ", `${n} mm`, "", r),
              W(s - v, l, s - v, l + u, "H.Q: ", `${a} mm`, "vertical", r),
              W(
                m - v,
                l + u,
                m - v,
                l + u + p,
                "H.T: ",
                `${i} mm`,
                "vertical",
                r
              );
          }
          function R(t, e, o, n = 1, a = 0, i = 0) {
            const r = M(t),
              c = M(e);
            let s = a,
              l = i;
            2 === n && ((s = a - r), (l = i - r));
            const d = [],
              _ = Math.PI / o;
            let u, p;
            for (let t = 0; t < 2 * o; t++) {
              const e = t * _ - Math.PI / 2,
                o = t % 2 == 0 ? r : c,
                n = o * Math.cos(e),
                a = o * Math.sin(e);
              d.push([n + s + r, a + l + r]),
                0 === t && (u = { x: n + s + r, y: a + l + r }),
                1 === t && (p = { x: n + s + r, y: a + l + r });
            }
            const m = d.map((t) => t.join(",")).join(" ");
            x.polygon(m).attr(k(n));
            if (1 === n) {
              const t = Math.max(r, c);
              Y(2 * t + s, 2 * t + l);
            } else 2 === n && 10;
            const h = s + r,
              f = l + r;
            W(h, f, u.x, u.y, "Rayon extérieur: ", `${t} mm`, "horizontal", n),
              W(
                h,
                f,
                p.x,
                p.y,
                "Rayon intérieur: ",
                `${e} mm`,
                "horizontal",
                n
              );
          }
          function A(t, e, o = 1, n = 0, a = 0) {
            const i = t / 9250,
              r = e / 12788;
            let s = n,
              l = a;
            2 === o ? ((s = n), (l = a)) : ((s = n + t / 2), (l = a + e / 2));
            const d =
              "\n                    M4625 12788 c-247 -47 -315 -62 -420 -92 -238 -70 -524 -196 -755 -333 -1361 -812 \n                    -2398 -2492 -3038 -4923 -189 -717 -316 -1380 -366 -1915 -54 -565 -53 -880 0 -1380 \n                    169 -1582 985 -2781 2380 -3497 609 -312 1313 -529 1994 -613 69 -8 154 -20 189 -26 \n                    48 -9 84 -9 145 0 45 6 141 18 214 26 682 78 1422 323 2007 666 1014 592 1702 1496 \n                    2036 2674 128 450 188 828 229 1445 22 326 25 513 10 688 -5 65 -14 200 -20 302 \n                    -25 431 -93 973 -155 1250 -100 442 -265 1016 -420 1464 -631 1826 -1476 3124 \n                    -2453 3770 -394 260 -797 416 -1217 471 -66 8 -148 20 -181 25 -71 11 -109 11 -179 -2z\n                ";
            x.path(d)
              .transform(`scale(${i}, ${r}) translate(${s}, ${l})`)
              .attr(k(o));
            const _ = c;
            1 === o ? Y(t, e) : 2 === o && (_ -= 10),
              W(
                s - t / 2,
                l + e / 2 + _,
                s + t / 2,
                l + e / 2 + _,
                "Largeur: ",
                `${t} mm`,
                "",
                o
              ),
              W(
                s - t / 2 - _,
                l - e / 2,
                s - t / 2 - _,
                l + e / 2,
                "Hauteur: ",
                `${e} mm`,
                "vertical",
                o
              );
          }
          async function z(t, o, a) {
            N();
            const i = M(a),
              r = (i / t.length) * 2,
              c = o,
              s = (await opentype.load(c)).getPath(t, 0, r, r).toPathData();
            try {
              e.path(s).attr({
                fill: "#ADEFFF",
                stroke: "#065075",
                "stroke-width": 1,
              });
            } catch (n) {
              console.log(n),
                e
                  .text(i / 2, r, t)
                  .attr({
                    "font-family": o,
                    "font-size": r,
                    fill: "#ADEFFF",
                    stroke: "#065075",
                    "stroke-width": 1,
                    "text-anchor": "middle",
                  });
            }
            let l = 0;
            1 === n.start && ((l = 600), (n.start = 0)),
              setTimeout(() => {
                const t = e.getBBox();
                let o = t.width,
                  n = t.height;
                var i = (a * n) / o;
                w(a, i, 0, "Rectangle"), (p = 2 * (a + i));
                const r = $("#demension_height").is(":checked");
                var c = a,
                  s = i;
                r && ((s = a), (c = (a * a) / i)),
                  W(
                    t.x,
                    t.y - 30,
                    t.x + o,
                    t.y - 30,
                    "L: ",
                    `${c.toFixed(2)} mm`
                  ),
                  W(
                    t.x + o + 30,
                    t.y,
                    t.x + o + 30,
                    t.y + n,
                    "H: ",
                    `${s.toFixed(2)} mm`,
                    "vertical"
                  ),
                  Y(o + 30, n);
              }, l),
              O(),
              f();
          }
          function N() {
            if (0 === $("#svgLoader").length) {
              const t = $("<div>", { id: "svgLoader" }).css({
                position: "absolute",
                top: "50%",
                left: "50%",
                transform: "translate(-50%, -50%)",
                width: "40px",
                height: "40px",
                border: "4px solid rgba(0, 0, 0, 0.1)",
                borderTop: "4px solid #333",
                borderRadius: "50%",
                animation: "spin 1s linear infinite",
                zIndex: 1e3,
              });
              $("#svgContainer").css("position", "relative").append(t),
                $("<style>")
                  .prop("type", "text/css")
                  .html(
                    "\n                            @keyframes spin {\n                                0% { transform: rotate(0deg); }\n                                100% { transform: rotate(360deg); }\n                            }\n                        "
                  )
                  .appendTo("head");
            }
            $("#svgLoader").show();
          }
          function O() {
            $("#svgLoader").remove();
          }
          function W(t, e, o, n, a = "", i = "", r = "horizontal", c = 1) {
            V(t, e, o, n, c);
            var s = 1 == c ? 13 : 2 == c ? 10 : 8;
            2 == c && (s = 10);
            const l = (t + o) / 2,
              d = (e + n) / 2;
            (a || i) &&
              G(
                a,
                i,
                l,
                d + 10,
                () => ({ "font-size": s, fill: "#065075" }),
                r,
                l,
                d,
                c
              );
          }
          function V(t, e, o, n) {
            const a = 5,
              i = { stroke: "#F15735", "stroke-width": 1 },
              r = Math.atan2(n - e, o - t) + Math.PI;
            function c(t, e, o) {
              const n = t - a * Math.cos(o + Math.PI / 4),
                r = e - a * Math.sin(o + Math.PI / 4),
                c = t - a * Math.cos(o - Math.PI / 4),
                s = e - a * Math.sin(o - Math.PI / 4);
              h.line(t, e, n, r).attr(i), h.line(t, e, c, s).attr(i);
            }
            h.line(t, e, o, n).attr(i), c(t, e, r), c(o, n, r + Math.PI);
          }
          function Z(t, e, n, a, i, r = 0, c = 0, s = 0) {
            let l = { stroke: "#000", strokeWidth: 1, strokeDasharray: "1, 1" },
              d = { x: parseFloat(t) + parseFloat(n), y: e },
              _ = { x: parseFloat(t) - parseFloat(n), y: e };
            o.line(d.x, d.y, d.x, -a - 7.5).attr(l),
              o.line(_.x, _.y, _.x, -a - 7.5).attr(l),
              W(
                d.x,
                -a - 7.5,
                _.x,
                -a - 7.5,
                "Diam : ",
                `${b(n)} mm`,
                "horizontal",
                3
              ),
              0 !== i
                ? (o.circle(c, s - a, 2),
                  o.line(c, -a, c, s - a).attr(l),
                  W(-5, s - a, -5, -a, "Y: ", `${b(s)} mm`, "vertical", 3),
                  o.line(c, s - a, 0, s - a).attr(l),
                  W(c, -5 - a, 0, -5 - a, "X: ", `${b(c)} mm`, "horizontal", 3),
                  o.line(c, s - a, c, s - a + i).attr(l),
                  o.line(c, s + i - a, 0, s + i - a).attr(l),
                  W(
                    -5,
                    s - a + i,
                    -5,
                    s + -a,
                    "D: ",
                    `${b(s)} mm`,
                    "vertical",
                    3
                  ))
                : (o.line(t, e, 0, e).attr(l),
                  W(-5, e, -5, -a, "Dist: ", `${b(r)} mm`, "vertical", 3),
                  o.circle(t, e, 1));
          }
          function G(t, e, o, n, a, i = "horizontal", r = 0, c = 0, s) {
            const l = 1 === s ? 2 : 1,
              d =
                1 === s
                  ? "rgba(97, 241, 150, 0.73)"
                  : 3 === s
                  ? "rgba(255, 165, 0, 0.73)"
                  : "rgba(83, 246, 181, 0.73)",
              _ = 1 === s ? 9 : 6,
              u = 3 === s ? -12 : 0,
              p = h.text(o, n - _, [t, e]).attr(a());
            p.select("tspan:nth-child(2)").attr({ "font-weight": "bold" }),
              setTimeout(() => {
                const t = p.getBBox(),
                  e = t.width,
                  a = t.height,
                  s = o - e / 2,
                  _ = n - a / 2;
                p.attr({ x: s, y: _ + u });
                const m = h
                  .rect(t.x - e / 2 - l, t.y - l + u, e + 2 * l, a + 3 * l, 5)
                  .attr({ fill: d });
                p.before(m);
                const x = "vertical" === i ? -90 : "depth" === i ? -45 : 0;
                if (0 !== x) {
                  const t = `rotate(${x} ${r} ${c})`;
                  p.transform(t), m.transform(t);
                }
                f();
              }, 0);
          }
          function X(t, e = 0) {
            if ($(`#${t}`).length > 0) {
              const o = $(`#${t}`).val(),
                n = parseFloat(o);
              return ["text_25", "text_26", "text_40", "text_41"].includes(t)
                ? isNaN(n)
                  ? e
                  : n
                : isNaN(n)
                ? e
                : Math.abs(n);
            }
            return e;
          }
          function Y(e, o, a = null) {
            const i = a ? n.x - e / 2 : n.x,
              r = a ? n.y - o / 2 : n.y,
              c = 35;
            t.attr({ viewBox: `${i - c} ${r - c} ${e + 2 * c} ${o + 2 * c}` });
          }
          P(a.type, v), I(i.type), f();
        })(),
        D(),
        W(),
        "undefined" != typeof updateTotale && updateTotale(),
        14 === n.type &&
          ($("#js_icp_next_opt_6").click(),
          $("#js_icp_next_opt_81").click(),
          $("#js_icp_next_opt_82").click(),
          $("#js_icp_next_opt_83").click());
    }
    function w(t, e, n = 0, r = "Rectangle") {
      if (
        ((a.width = t), (a.height = e), (i.width = t), (i.height = e), o.on)
      ) {
        (o.vitrineWidth = t / 1e3),
          (o.vitrineHeight = e / 1e3),
          (o.vitrineDepth = n / 1e3);
        const a = e + o.socleHeight;
        (_ = Math.max(t, a, n)),
          (u = Math.min(t, a, n)),
          (d = t + a + n - _ - u);
      } else (_ = Math.max(t, e)), (d = Math.min(t, e));
      $("#product-title-unique-12345").text(r),
        o.on
          ? $("#product-size-unique-12345").text(
              `${d.toFixed(2)} x ${_.toFixed(2)} x ${u.toFixed(2)} mm`
            )
          : $("#product-size-unique-12345").text(
              `${d.toFixed(2)} x ${_.toFixed(2)} mm`
            );
    }
    function y(t, e = 0) {
      const o = $(`#${t}`).val(),
        n = parseFloat(o);
      return ["text_25", "text_26", "text_40", "text_41"].includes(t)
        ? isNaN(n)
          ? e
          : n
        : isNaN(n)
        ? e
        : Math.abs(n);
    }
    function P(t, e) {
      const o = null !== e ? e : "";
      $(`#${t}`).val(o);
    }
    function I(t) {
      $(`#resume_price_block_${t}_1 .option_title`).html("Non séléctionné"),
        $(`#js_opt_${t}_value`).html("false"),
        $(`#js_opt_extra_${t}_value`).html("false"),
        $(`#js_opt_${t}_value_wqty`).html("false"),
        $(`#step_title_${t} .check`).removeClass("check");
    }
    function C() {
        function t() {
          const t = $("#quantity_unique_12345").val();
          $("#idxrcustomprouct_quantity_wanted").val(t).trigger("input"),
            "undefined" != typeof updateTotal && updateTotal();
        }
        var e, a;
        $(".accordion_text").on("input", function () {
          if (
            (function (t) {
              let e = !1;
              function a() {
                var t = y("text_53"),
                  o = y("text_55"),
                  n = y("text_54"),
                  a = 2 * y("text_56") + o,
                  r = i(
                    t + n,
                    50,
                    2050,
                    "#text_53, #text_54",
                    "La largeur totale doit être entre 50 mm et 2050 mm"
                  ),
                  c = i(
                    a,
                    50,
                    1540,
                    "#text_55, #text_56",
                    "La hauteur totale doit être entre 50 mm et 1540 mm"
                  );
                r && c && (b(), (e = !0));
              }
              function i(t, e, o, n, a) {
                return !(t < e || t > o) || (q(n, a), !1);
              }
              let r = $("#" + t),
                c = r.val(),
                s = H[t],
                l = r.closest(".card-block");
              l.find(".measurements-selector__error").remove(),
                l.removeClass("input-section--invalid"),
                "" === c
                  ? q("#" + t, "Ce champ ne doit pas être vide")
                  : s
                  ? 7 == n.type &&
                    ("text_10" == t || "text_3" == t) &&
                    y("text_10") < y("text_3") / 2
                    ? q(
                        "#" + t,
                        "La hauteur doit être supérieure à la moitié de la largeur."
                      )
                    : c < s.min || c > s.max
                    ? q(
                        "#" + t,
                        `La valeur doit être entre ${s.min} mm et ${s.max} mm`
                      )
                    : (o.on ? A() : b(), (e = !0))
                  : 10 == n.type || 7 == n.type
                  ? a()
                  : (o.on ? A() : b(), (e = !0));
              return e;
            })(this.id)
          ) {
            $(this).closest(".step_content").addClass("finished");
            var t = $(this).attr("id").replace("text_", "");
            $("#js_icp_next_opt_" + t).click();
          }
        }),
          $(".accordion_text").on("change", function () {
            const t = $(this).attr("id"),
              e = H[t];
            if (e) {
              let t = parseFloat($(this).val());
              isNaN(t) || t < e.min
                ? $(this).val(e.min)
                : t > e.max && $(this).val(e.max);
            }
          }),
          $("#quantity_unique_12345").on("input", function () {
            t();
          }),
          $(".qty-change-unique-12345.increase").on("click", function () {
            let e = parseInt($("#quantity_unique_12345").val());
            (e += 1), $("#quantity_unique_12345").val(e).trigger("input"), t();
          }),
          $(".qty-change-unique-12345.decrease").on("click", function () {
            let e = parseInt($("#quantity_unique_12345").val());
            e > 1 &&
              ((e -= 1),
              $("#quantity_unique_12345").val(e).trigger("change"),
              t());
          }),
          $("#add-to-cart-button-unique-12345").on("click", function () {
            $("#add-to-cart-button-unique-12345").prop("disabled", !0),
              t(),
              $("#idxrcustomproduct_send").trigger("click");
          }),
          $(".increase").click(function () {
            let e = parseInt($("#quantity_custom_12345").val());
            $("#quantity_custom_12345").val(e + 1), t();
          }),
          $(".decrease").click(function () {
            let e = parseInt($("#quantity_custom_12345").val());
            e > 1 && ($("#quantity_custom_12345").val(e - 1), t());
          }),
          $("#toggleTableLink").click(function (t) {
            t.preventDefault(), $("#collapsibleSection").toggle();
          }),
          $(".zoom-in").click(function () {
            var t = $("#actualSvg"),
              e = t.data("scale") || 1;
            t.data("scale", e + 0.1), t.css("transform", `scale(${e + 0.1})`);
          }),
          $(".zoom-out").click(function () {
            var t = $("#actualSvg"),
              e = t.data("scale") || 1;
            e > 0.1 && t.data("scale", e - 0.1),
              t.css("transform", `scale(${e - 0.1})`);
          }),
          $(".rotateright").click(function () {
            var t = $("#actualSvg"),
              e = t.data("rotate") || 0;
            t.data("rotate", e + 90), t.css("transform", `rotate(${e + 90}deg)`);
          }),
          $(".rotateleft").click(function () {
            var t = $("#actualSvg"),
              e = t.data("rotate") || 0;
            t.data("rotate", e - 90), t.css("transform", `rotate(${e - 90}deg)`);
          }),
          t(),
          (e = $("#submit_idxrcustomproduct_alert")),
          (a = $(".braig_addtocart_section")),
          e.length && a.length && e.prependTo(a),
          (function () {
            var t =
              '\n                <div class="container mt-2" id="containerOfBioDetails">\n                    <div class="row" id="wk-sample-block-row"></div>\n                    <div class="row" id="product-short-desc-row"></div>\n                </div>\n                ';
            $(window).width() >= 992
              ? $(".col-lg-6.col-image").append(t)
              : $("#product-container-bottom").prepend(t);
            var e = new MutationObserver(function () {
              // Commenter ou supprimer ces lignes pour empêcher le déplacement de .wk-sample-block
              // $(".wk-sample-block").length &&
              //   $(".wk-sample-block").each(function () {
              //     $("#wk-sample-block-row").append($(this));
              //   });
      
              var t = $("body")
                .attr("class")
                .match(/product-id-(\d+)/);
              if (t) {
                t = t[1];
                var o = $("#product-description-short-" + t);
      
                // Commenter ou supprimer ces lignes pour empêcher le déplacement de #product-description-short
                // o.length &&
                //   ($("#product-short-desc-row").append(o),
                //   e.disconnect(),
                //   $(".card-header .fa-info-circle").hide());
              }
            });
            e.observe(document.body, { childList: !0, subtree: !0 });
          })();
        new MutationObserver((t, e) => {
          document.querySelector(".owl-stage-outer .owl-stage") &&
            (!(function () {
              !(function (t, e) {
                const o = document.createElement("div");
                (o.className = "owl-item active"),
                  (o.style.width = "188.919px"),
                  (o.style.marginRight = "22px"),
                  (o.id = e);
                const n = document.createElement("li");
                n.className = "thumb-container";
                const a = document.createElement("img");
                (a.className = "thumb js-thumb"),
                  (a.src = t),
                  a.setAttribute("data-image-medium-src", t),
                  a.setAttribute("data-image-large-src", t),
                  (a.alt = ""),
                  (a.title = ""),
                  (a.width = 100),
                  (a.itemprop = "image"),
                  n.appendChild(a),
                  o.appendChild(n);
                const i = document.querySelector(".owl-stage-outer .owl-stage");
                i &&
                  ((i.style.width =
                    parseFloat(i.style.width) + 188.919 + 22 + "px"),
                  i.appendChild(o));
              })("/modules/idxrcustomproduct/img/icon/2d.svg", "TwoDImageThumb");
              const t = document.querySelectorAll(".owl-stage .thumb.js-thumb");
              t.forEach((e, o) => {
                const n = e.closest(".owl-item");
                o !== t.length - 1 || (n && "TwoDImageThumb" !== n.id)
                  ? e.addEventListener("click", S)
                  : e.addEventListener("click", D);
              });
            })(),
            e.disconnect());
        }).observe(document.body, { childList: !0, subtree: !0 }),
          $(window).resize(function () {
            "undefined" != typeof idxcp_console_state &&
              (idxcp_console_state = 1);
          });
      }
    function L(t = !1) {
      if (
        (["76", "77", "78", "87"].forEach(function (t) {
          $("#step_title_" + t).addClass("in"),
            $("#component_step_" + t + " a").each(function () {
              $(this).removeAttr("href"), $(this).removeAttr("data-toggle");
            });
        }),
        t)
      ) {
        ["92", "107"].forEach(function (t) {
          $("#step_title_" + t).addClass("in");
        });
      }
    }
    function B() {
      !(function () {
        let t = document.getElementById("titleHolder");
        t || ((t = document.createElement("div")), (t.id = "titleHolder"));
        const e = document.querySelector(
          ".col-lg-6.col-content .col-content-inside"
        );
        var n, a;
        e
          ? ((n = ".price-information"),
            (a = () => {
              if (o.on) $(".price-information").hide();
              else {
                const o = document.querySelector(".price-information"),
                  n = document.querySelector(".info-prod"),
                  a = document.querySelector(
                    ".product-comments-additional-info"
                  );
                // [document.querySelector(".h1.product-title"), a, n, o].forEach(
                //   (e) => {
                //     e && t.appendChild(e);
                //   }
                // ),
                //   e.firstChild
                //     ? e.insertBefore(t, e.firstChild)
                //     : e.appendChild(t);
              }
            }),
            new MutationObserver((t, e) => {
              document.querySelector(n) && (e.disconnect(), a());
            }).observe(document.body, { childList: !0, subtree: !0 }))
          : console.error("Parent element not found");
      })(),
        (function () {
          const t = document.createElement("div");
          (t.className = "svg-cover hidden"),
            (t.id = "svgContainer"),
            (t.innerHTML =
              '\n                    <svg id="actualSvg" width="400" height="400" style=" width: 100%; height: 100%;">\n                        <g id="shapeContainer"></g>\n                        <g id="holesContainer"></g>\n                        <g id="couOutMain">\n                            <g id="cutoutContainer"></g>\n                            <g id="cutoutDems" class="activeDemensions"></g>\n                        </g>\n                        <g id="arrowsContainer" class="activeDemensions"></g>\n                    </svg>\n                ');
          const e = document.querySelector(".product-cover");
          e
            ? e.insertAdjacentElement("afterend", t)
            : console.error("Error: .product-cover element not found");
        })(),
        o.on
          ? ($("#resume_tr_poids").after(
              '\n                    <tr>\n                        <td>Le prix de la vitrine </td>\n                        <td></td>\n                        <td id="tr_resume_prix_de_capot">0 €</td>\n                    </tr>\n                    <tr>\n                        <td>Le prix du socle </td>\n                        <td></td>\n                        <td id="tr_resume_prix_de_socle">0 €</td>\n                    </tr>\n                '
            ),
            (function () {
              L(!0);
              var t = $(
                '<div class="col-lg-12 component_step" id="fieldsBoxDemesions"></div>'
              );
              t.insertAfter("#component_step_86");
              var e = $('<div class="movedDivsHolder"></div>');
              e.appendTo(t),
                $(
                  "#component_step_91, #component_step_78, #component_step_76, #component_step_77, #component_step_87"
                ).appendTo(e);
            })(),
            (function () {
              const t = $("<div>", {
                class: "divider",
                id: "parametres_de_vitrine",
              }).append($("<span>").text("Paramètres de Vitrine"));
              $("#component_step_92").before(t);
              const e = $("<div>", {
                class: "divider",
                id: "parametres_de_socle",
              }).append($("<span>").text("Parametres de Socle"));
              $("#component_step_85").before(e);
              const o = $("<div>", {
                class: "divider",
                id: "parametres_de_dems",
              }).append($("<span>").text("Les Dimensions Extérieures"));
              $("#fieldsBoxDemesions").before(o);
            })(),
            ["text_76", "text_77", "text_78", "text_87"].forEach((t) => {
              const e = document.getElementById(t);
              if (e) {
                const t = document.createElement("span");
                (t.className = "unit"),
                  (t.textContent = "mm"),
                  e.parentNode.insertBefore(t, e.nextSibling);
              } else console.error("Element not found:", t);
            }),
            $("#svgContainer").prepend(
              '\n                <div class="svg-controls">\n                    <button class="svg-btn zoom-in"><img src="/modules/idxrcustomproduct/img/icon/p.png" alt="rg"></button>\n                    <button class="svg-btn zoom-out"><img src="/modules/idxrcustomproduct/img/icon/m.png" alt="rg"></button>\n                    <button class="svg-btn rotateright"><img src="/modules/idxrcustomproduct/img/icon/rr.png" alt="rg"></i></button>\n                    <button class="svg-btn rotateleft"><img src="/modules/idxrcustomproduct/img/icon/rl.png" alt="rg"></button>\n                </div>'
            ),
            $.each(
              {
                card_92_0:
                  "/accueil/10157-vitrine-plexiglass-sur-mesure-epaisseur-4mm.html",
                card_92_1:
                  "/accueil/10158-vitrine-plexiglass-sur-mesure-epaisseur-5mm.html",
                card_92_2:
                  "/accueil/10159-vitrine-plexiglass-sur-mesure-epaisseur-6mm.html",
                card_92_3:
                  "/accueil/10160-vitrine-plexiglass-sur-mesure-epaisseur-8mm.html",
                card_92_4:
                  "/accueil/10161-vitrine-plexiglass-sur-mesure-epaisseur-10mm.html",
              },
              function (t, e) {
                const o = $("#" + t);
                0 === o.find(".check").length &&
                  o.on("click", function () {
                    window.location.href = e;
                  });
              }
            ),
            $(".price-information").hide())
          : (!(function () {
              function t(t, e) {
                t.forEach((t) => {
                  let o = document.getElementById(t);
                  o &&
                    (e.appendChild(o),
                    o.querySelectorAll("a").forEach((t) => {
                      t.removeAttribute("href"),
                        t.removeAttribute("data-toggle");
                    }),
                    o.querySelectorAll(".collapse").forEach((t) => {
                      t.classList.remove("collapse"),
                        t.removeAttribute("aria-expanded"),
                        t.removeAttribute("style");
                    }));
                });
              }
              function e(e, o, n, a = []) {
                let i = document.getElementById(e);
                if (i) {
                  let e = i.querySelector(".card.step_content"),
                    r = e?.querySelector(".card-block");
                  if (r) {
                    let e = (function (t, e) {
                      let o = document.createElement("div");
                      return (
                        (o.id = e),
                        (o.className = "movedDivsHolder"),
                        t.appendChild(o),
                        o
                      );
                    })(r, n);
                    t(a, e), t(o, e);
                  }
                }
              }
              document
                .querySelectorAll(".card.step_content .panel-collapse")
                .forEach((t) => {
                  t.classList.remove("panel-collapse");
                }),
                (function () {
                  const t = $('<div id="step_2_options"></div>'),
                    e = $('<div id="step_2_preview"></div>');
                  $("#step_title_2 .card-block").prepend(t),
                    $("#step_2_options").after(e),
                    $("#card_2_0, #card_2_1, #card_2_2, #card_2_3").appendTo(
                      "#step_2_options"
                    );
                  const o = $('<div id="step_17_options"></div>'),
                    n = $('<div id="step_17_preview"></div>');
                  $("#step_title_17 .card-block").prepend(o),
                    $("#step_17_options").after(n),
                    $(
                      "#card_17_0, #card_17_1, #card_17_2, #card_17_3"
                    ).appendTo("#step_17_options");
                  const a = $('<div id="step_29_options"></div>'),
                    i = $('<div id="step_29_preview"></div>');
                  $("#step_title_29 .card-block").prepend(a),
                    $("#step_29_options").after(i),
                    $("#card_29_0, #card_29_1").appendTo("#step_29_options");
                })(),
                e(
                  "component_step_2",
                  [
                    "component_step_6",
                    "component_step_83",
                    "component_step_3",
                    "component_step_4",
                    "component_step_109",
                    "component_step_110",
                    "component_step_111",
                    "component_step_112",
                    "component_step_9",
                    "component_step_10",
                    "component_step_11",
                    "component_step_12",
                    "component_step_13",
                    "component_step_53",
                    "component_step_54",
                    "component_step_56",
                    "component_step_57",
                    "component_step_58",
                    "component_step_55",
                  ],
                  "fieldsHolderStepOne",
                  ["component_step_5"]
                ),
                e(
                  "component_step_17",
                  [
                    "component_step_14",
                    "component_step_15",
                    "component_step_16",
                    "component_step_18",
                    "component_step_19",
                    "component_step_20",
                    "component_step_21",
                    "component_step_22",
                    "component_step_23",
                    "component_step_24",
                    "component_step_25",
                    "component_step_26",
                    "component_step_27",
                    "component_step_28",
                  ],
                  "fieldsHolderStepHoles"
                ),
                e(
                  "component_step_29",
                  [
                    "component_step_32",
                    "component_step_33",
                    "component_step_34",
                    "component_step_35",
                    "component_step_36",
                    "component_step_37",
                    "component_step_38",
                    "component_step_39",
                    "component_step_63",
                    "component_step_40",
                    "component_step_41",
                    "component_step_42",
                    "component_step_113",
                    "component_step_114",
                    "component_step_115",
                    "component_step_116",
                    "component_step_44",
                    "component_step_45",
                    "component_step_46",
                    "component_step_47",
                    "component_step_48",
                    "component_step_64",
                    "component_step_65",
                    "component_step_66",
                    "component_step_67",
                    "component_step_68",
                    "component_step_69",
                  ],
                  "fieldsHolderStepCut",
                  ["component_step_31"]
                ),
                e(
                  "component_step_62",
                  ["component_step_70", "component_step_71"],
                  "fieldsHolderStep2"
                );
            })(),
            [
              "3",
              "23",
              "4",
              "9",
              "10",
              "11",
              "57",
              "58",
              "53",
              "54",
              "55",
              "56",
              "18",
              "25",
              "26",
              "27",
              "38",
              "64",
              "63",
              "39",
              "65",
              "40",
              "41",
              "42",
              "75",
              "45",
              "66",
              "67",
              "68",
              "69",
            ].forEach((t) => {
              const e = document.getElementById(`text_${t}`);
              if (e) {
                const o = document.createElement("span");
                (o.className = "unit"),
                  (o.textContent = "42" === t ? "deg" : "mm"),
                  e.parentNode.insertBefore(o, e.nextSibling);
              } else console.error("Element not found: text_", t);
            }),
            $("#svgContainer").prepend(
              '\n                <div class="svg-controls">\n                    <button class="svg-btn zoom-in"><img src="/modules/idxrcustomproduct/img/icon/p.png" alt="rg"></button>\n                    <button class="svg-btn zoom-out"><img src="/modules/idxrcustomproduct/img/icon/m.png" alt="rg"></button>\n                    <button class="svg-btn rotateright"><img src="/modules/idxrcustomproduct/img/icon/rr.png" alt="rg"></i></button>\n                    <button class="svg-btn rotateleft"><img src="/modules/idxrcustomproduct/img/icon/rl.png" alt="rg"></button>\n\n                    <div class="qwerty-switch-container">\n                        <p id="switchStatus">Dimensions:</p>\n                        <div class="qwerty-switch-wrapper active">\n                            <div class="qwerty-switch-bg">\n                                <div class="qwerty-switch-circle"></div>\n                            </div>\n                        </div>\n                    </div>\n                </div>'
            ),
            "undefined" != typeof loadimages && loadimages($("#step_title_61")),
            $("#step_title_61").addClass("in"),
            $("#text_6").attr("type", "text"),
            $("#text_6").val("Louis").trigger("input"),
            $("#text_81").attr("type", "text"),
            $("#text_81")
              .val(
                "/modules/idxrcustomproduct/views/js/fonts/Alfa_Slab_One/AlfaSlabOne-Regular.ttf"
              )
              .trigger("input"),
            $("#component_step_6").prepend(
              "<div class=\"font-selector-container\"><label for=\"fontSelector\" style=\"margin-right: 10px;\">Select Font:</label>\n                <select id=\"fontSelector\" class=\"font-selector\">\n                    <option value='' disabled selected>Select a font</option>\n                    <option value='/modules/idxrcustomproduct/views/js/fonts/Afacad_Flux/AfacadFlux-VariableFont_slnt,wght.ttf'>Afacad_Flux - AfacadFlux-VariableFont_slnt,wght</option>\n                    <option value='/modules/idxrcustomproduct/views/js/fonts/Alfa_Slab_One/AlfaSlabOne-Regular.ttf'>Alfa_Slab_One - AlfaSlabOne-Regular</option>\n                    <option value='/modules/idxrcustomproduct/views/js/fonts/Anton_SC/AntonSC-Regular.ttf'>Anton_SC - AntonSC-Regular</option>\n                    <option value='/modules/idxrcustomproduct/views/js/fonts/Chakra_Petch/ChakraPetch-Bold.ttf'>Chakra_Petch - ChakraPetch-Bold</option>\n                    <option value='/modules/idxrcustomproduct/views/js/fonts/Chakra_Petch/ChakraPetch-BoldItalic.ttf'>Chakra_Petch - ChakraPetch-BoldItalic</option>\n                    <option value='/modules/idxrcustomproduct/views/js/fonts/Chakra_Petch/ChakraPetch-Italic.ttf'>Chakra_Petch - ChakraPetch-Italic</option>\n                    <option value='/modules/idxrcustomproduct/views/js/fonts/Chakra_Petch/ChakraPetch-Light.ttf'>Chakra_Petch - ChakraPetch-Light</option>\n                    <option value='/modules/idxrcustomproduct/views/js/fonts/Chakra_Petch/ChakraPetch-LightItalic.ttf'>Chakra_Petch - ChakraPetch-LightItalic</option>\n                    <option value='/modules/idxrcustomproduct/views/js/fonts/Chakra_Petch/ChakraPetch-Medium.ttf'>Chakra_Petch - ChakraPetch-Medium</option>\n                    <option value='/modules/idxrcustomproduct/views/js/fonts/Chakra_Petch/ChakraPetch-MediumItalic.ttf'>Chakra_Petch - ChakraPetch-MediumItalic</option>\n                    <option value='/modules/idxrcustomproduct/views/js/fonts/Chakra_Petch/ChakraPetch-Regular.ttf'>Chakra_Petch - ChakraPetch-Regular</option>\n                    <option value='/modules/idxrcustomproduct/views/js/fonts/Chakra_Petch/ChakraPetch-SemiBold.ttf'>Chakra_Petch - ChakraPetch-SemiBold</option>\n                    <option value='/modules/idxrcustomproduct/views/js/fonts/Chakra_Petch/ChakraPetch-SemiBoldItalic.ttf'>Chakra_Petch - ChakraPetch-SemiBoldItalic</option>\n                    <option value='/modules/idxrcustomproduct/views/js/fonts/cs-vasco-font-1730655040-0/CSVasco-Regular_demo-BF672337323e31e.otf'>cs-vasco-font-1730655040-0 - CSVasco-Regular</option>\n                    <option value='/modules/idxrcustomproduct/views/js/fonts/Dancing_Script/DancingScript-VariableFont_wght.ttf'>Dancing_Script - DancingScript-VariableFont_wght</option>\n                    <option value='/modules/idxrcustomproduct/views/js/fonts/Danfo/Danfo-Regular-VariableFont_ELSH.ttf'>Danfo - Danfo-Regular-VariableFont_ELSH</option>\n                    <option value='/modules/idxrcustomproduct/views/js/fonts/IBM_Plex_Mono/IBMPlexMono-Bold.ttf'>IBM_Plex_Mono - IBMPlexMono-Bold</option>\n                    <option value='/modules/idxrcustomproduct/views/js/fonts/IBM_Plex_Mono/IBMPlexMono-BoldItalic.ttf'>IBM_Plex_Mono - IBMPlexMono-BoldItalic</option>\n                    <option value='/modules/idxrcustomproduct/views/js/fonts/IBM_Plex_Mono/IBMPlexMono-ExtraLight.ttf'>IBM_Plex_Mono - IBMPlexMono-ExtraLight</option>\n                    <option value='/modules/idxrcustomproduct/views/js/fonts/IBM_Plex_Mono/IBMPlexMono-ExtraLightItalic.ttf'>IBM_Plex_Mono - IBMPlexMono-ExtraLightItalic</option>\n                    <option value='/modules/idxrcustomproduct/views/js/fonts/IBM_Plex_Mono/IBMPlexMono-Italic.ttf'>IBM_Plex_Mono - IBMPlexMono-Italic</option>\n                    <option value='/modules/idxrcustomproduct/views/js/fonts/IBM_Plex_Mono/IBMPlexMono-Light.ttf'>IBM_Plex_Mono - IBMPlexMono-Light</option>\n                    <option value='/modules/idxrcustomproduct/views/js/fonts/IBM_Plex_Mono/IBMPlexMono-LightItalic.ttf'>IBM_Plex_Mono - IBMPlexMono-LightItalic</option>\n                    <option value='/modules/idxrcustomproduct/views/js/fonts/IBM_Plex_Mono/IBMPlexMono-Medium.ttf'>IBM_Plex_Mono - IBMPlexMono-Medium</option>\n                    <option value='/modules/idxrcustomproduct/views/js/fonts/IBM_Plex_Mono/IBMPlexMono-MediumItalic.ttf'>IBM_Plex_Mono - IBMPlexMono-MediumItalic</option>\n                    <option value='/modules/idxrcustomproduct/views/js/fonts/IBM_Plex_Mono/IBMPlexMono-Regular.ttf'>IBM_Plex_Mono - IBMPlexMono-Regular</option>\n                    <option value='/modules/idxrcustomproduct/views/js/fonts/IBM_Plex_Mono/IBMPlexMono-SemiBold.ttf'>IBM_Plex_Mono - IBMPlexMono-SemiBold</option>\n                    <option value='/modules/idxrcustomproduct/views/js/fonts/IBM_Plex_Mono/IBMPlexMono-SemiBoldItalic.ttf'>IBM_Plex_Mono - IBMPlexMono-SemiBoldItalic</option>\n                    <option value='/modules/idxrcustomproduct/views/js/fonts/IBM_Plex_Mono/IBMPlexMono-Thin.ttf'>IBM_Plex_Mono - IBMPlexMono-Thin</option>\n                    <option value='/modules/idxrcustomproduct/views/js/fonts/IBM_Plex_Mono/IBMPlexMono-ThinItalic.ttf'>IBM_Plex_Mono - IBMPlexMono-ThinItalic</option>\n                    <option value='/modules/idxrcustomproduct/views/js/fonts/Itim/Itim-Regular.ttf'>Itim - Itim-Regular</option>\n                    <option value='/modules/idxrcustomproduct/views/js/fonts/Lobster/Lobster-Regular.ttf'>Lobster - Lobster-Regular</option>\n                    <option value='/modules/idxrcustomproduct/views/js/fonts/magion-font-1730654999-0/magiontrial-italic.otf'>magion-font-1730654999-0 - magiontrial-italic</option>\n                    <option value='/modules/idxrcustomproduct/views/js/fonts/magion-font-1730654999-0/magiontrial-regular.otf'>magion-font-1730654999-0 - magiontrial-regular</option>\n                    <option value='/modules/idxrcustomproduct/views/js/fonts/Oswald/Oswald-VariableFont_wght.ttf'>Oswald - Oswald-VariableFont_wght</option>\n                    <option value='/modules/idxrcustomproduct/views/js/fonts/Pacifico/Pacifico-Regular.ttf'>Pacifico - Pacifico-Regular</option>\n                    <option value='/modules/idxrcustomproduct/views/js/fonts/Permanent_Marker/PermanentMarker-Regular.ttf'>Permanent_Marker - PermanentMarker-Regular</option>\n                    <option value='/modules/idxrcustomproduct/views/js/fonts/Playfair_Display/PlayfairDisplay-Italic-VariableFont_wght.ttf'>Playfair_Display - PlayfairDisplay-Italic-VariableFont_wght</option>\n                    <option value='/modules/idxrcustomproduct/views/js/fonts/Playfair_Display/PlayfairDisplay-VariableFont_wght.ttf'>Playfair_Display - PlayfairDisplay-VariableFont_wght</option>\n                    <option value='/modules/idxrcustomproduct/views/js/fonts/Playwrite_GB_S/PlaywriteGBS-Italic-VariableFont_wght.ttf'>Playwrite_GB_S - PlaywriteGBS-Italic-VariableFont_wght</option>\n                    <option value='/modules/idxrcustomproduct/views/js/fonts/Playwrite_GB_S/PlaywriteGBS-VariableFont_wght.ttf'>Playwrite_GB_S - PlaywriteGBS-VariableFont_wght</option>\n                    <option value='/modules/idxrcustomproduct/views/js/fonts/Rubik_Wet_Paint/RubikWetPaint-Regular.ttf'>Rubik_Wet_Paint - RubikWetPaint-Regular</option>\n                    <option value='/modules/idxrcustomproduct/views/js/fonts/seadoh-font-1730655129-0/seadoh-demolight.otf'>seadoh-font-1730655129-0 - seadoh-demolight</option>\n                    <option value='/modules/idxrcustomproduct/views/js/fonts/seadoh-font-1730655129-0/seadoh-demoregular.otf'>seadoh-font-1730655129-0 - seadoh-demoregular</option>\n                    <option value='/modules/idxrcustomproduct/views/js/fonts/Spicy_Rice/SpicyRice-Regular.ttf'>Spicy_Rice - SpicyRice-Regular</option>\n                    </select></div>"
            ),
            $("#fontSelector").on("change", function () {
              const t = $(this).val();
              $("#text_81").val(t).trigger("input");
            }),
            $("#component_step_83").prepend(
              '\n                    <div class="dem-toggle-switch">\n                        <span class="demension_label">Dimension souhaitée</span>\n                        <div class="demension_switch">\n                            <input type="radio" name="demension_dimension" id="demension_height" checked>\n                            <label for="demension_height" class="switch-label" id="heightLabel">Hauteur</label>\n                            <input type="radio" name="demension_dimension" id="demension_width">\n                            <label for="demension_width" class="switch-label" id="widthLabel">Largeur</label>\n                            <div class="slider"></div>\n                        </div>\n                    </div>'
            ),
            $('input[name="demension_dimension"]').change(function () {
              $("#demension_width").is(":checked")
                ? ($("#text_82")
                    .attr("type", "text")
                    .val("Largeur")
                    .trigger("input"),
                  $("#step_content_83 a:first").text("Largeur"))
                : ($("#text_82")
                    .attr("type", "text")
                    .val("Hauteur")
                    .trigger("input"),
                  $("#step_content_83 a:first").text("Hauteur"));
            })),
        $(".col-lg-6.col-image").addClass("custom-image");
    }
    function F(t, e) {
      var o = [],
        n = [];
      switch (e) {
        case 1:
          (o = h.find((e) => e.id === t)), (n = f);
          break;
        case 2:
          (o = v.find((e) => e.id === t)), (n = g);
          break;
        case 3:
          (o = k.find((e) => e.id === t)), (n = M);
      }
      o &&
        (n.forEach((t) => {
          I(t.split("_")[1]);
        }),
        o.fields.forEach((t) => {
          var e = $("#" + t);
          e.closest(".step_content").addClass("finished");
          var o = e?.attr("id")?.replace("text_", "");
          $("#js_icp_next_opt_" + o).click();
        }));
    }
    function S() {
      const t = document.getElementById("svgContainer"),
        e = document.querySelector(".product-cover");
      t && e
        ? (t.classList.add("hidden"), e.classList.remove("hidden"))
        : console.error("Error: One of the elements not found");
    }
    function D() {
      const t = document.getElementById("svgContainer"),
        e = document.querySelector(".product-cover");
      t && e
        ? (t.classList.remove("hidden"), e.classList.add("hidden"))
        : console.error("Error: One of the elements not found");
    }
    function E() {
      var t = Math.min(d, _) / 3,
        e = a.width / 2,
        o = a.height / 2;
      [
        { id: "text_18", default: 5 },
        { id: "text_21", default: 5 },
        { id: "text_22", default: 5 },
        { id: "text_23", default: 10 },
        { id: "text_25", default: parseFloat(e).toFixed(0) },
        { id: "text_26", default: parseFloat(o).toFixed(0) },
        { id: "text_27", default: parseFloat(t).toFixed(0) },
        { id: "text_28", default: 10 },
      ].forEach((t) => {
        P(t.id, t.default);
      });
    }
    function j() {
      const { width: t, height: e } = i;
      if (!t || !e) return;
      const o = t / 2,
        n = e / 2,
        a = Math.min(o, n),
        r = (a / 2).toFixed(0),
        c = (a / 5).toFixed(0),
        s = (a / 4).toFixed(0),
        l = (0.7 * o).toFixed(0),
        d = (o / 3).toFixed(0);
      [
        { id: "text_64", default: r },
        { id: "text_65", default: c },
        { id: "text_63", default: l },
        { id: "text_38", default: o.toFixed(0) },
        { id: "text_39", default: d },
        { id: "text_40", default: o.toFixed(0) },
        { id: "text_41", default: n.toFixed(0) },
        { id: "text_42", default: "0" },
        { id: "text_113", default: "0" },
        { id: "text_114", default: "0" },
        { id: "text_115", default: "0" },
        { id: "text_116", default: "0" },
        { id: "text_45", default: r },
        { id: "text_66", default: r },
        { id: "text_67", default: r },
        { id: "text_68", default: r },
        { id: "text_69", default: s },
      ].forEach(({ id: t, default: e }) => P(t, e));
    }
    function q(t, e) {
      var o = $(t).closest(".card-block");
      o.find(".measurements-selector__error").remove(),
        o.addClass("input-section--invalid");
      let n = `<span class="measurements-selector__error">\n                ${e}\n            </span>`;
      o.append(n);
    }
    const H = {
      text_3: { min: T("product_Largeur_min"), max: T("product_Largeur_max") },
      text_4: {
        min: T("product_longueur_min"),
        max: T("product_longueur_max"),
      },
      text_109: { min: 0, max: 1e3 },
      text_110: { min: 0, max: 1e3 },
      text_111: { min: 0, max: 1e3 },
      text_112: { min: 0, max: 1e3 },
      text_9: { min: T("product_Largeur_min"), max: T("product_Largeur_max") },
      text_10: { min: T("product_Largeur_min"), max: T("product_Largeur_max") },
      text_11: {
        min: T("product_longueur_min"),
        max: T("product_longueur_max"),
      },
      text_13: { min: 1, max: 100 },
      text_23: { min: 1, max: 100 },
      text_57: {
        min: T("product_longueur_min"),
        max: T("product_longueur_max") / 2,
      },
      text_58: {
        min: T("product_Largeur_min"),
        max: T("product_Largeur_max") / 2,
      },
      text_76: { min: 30, max: T("product_Largeur_max") },
      text_77: { min: 50, max: T("product_longueur_max") },
      text_78: { min: 50, max: parseFloat(R()) },
      text_87: { min: 15, max: parseFloat(R()) },
    };
    function T(t) {
      return 11 === n.type ? parseFloat(y(t, 800)) / 2 : parseFloat(y(t, 800));
    }
    function R() {
      const t = $(".data-sheet .name")
        .filter(function () {
          return "Hauteur maximale (mm)" === $(this).text().trim();
        })
        .next(".value")
        .text()
        .trim();
      return console.log(t), t && 0 !== parseFloat(t) ? t : "500";
    }
    function A(n = !1) {
      t.clear(),
        (e = t.select("#shapeContainer")) ||
          (e = t.group().attr({ id: "box" }));
      const a = y("text_76", 200),
        i = y("text_78", 200),
        r = y("text_87", 200),
        c = y("text_77", 200),
        s = o.scoleColor,
        l = r + i;
      function d(t, e) {
        const o = H[t];
        if (o) {
          const { min: t, max: n } = o;
          return e >= t && e <= n;
        }
        return !1;
      }
      if (!d("text_76", a) || !d("text_78", i) || !d("text_77", c)) return;
      (o.socleHeight = r),
        w(a, i, c, "Vitrine sur mesure"),
        (p = 4 === o.faces ? 2 * i + a + 2 * c : 4 * i + 2 * a + 2 * c);
      const _ = 200 / Math.max(a, l, c);
      var u = $("#svgContainer").width(),
        m = $("#svgContainer").height(),
        x = Math.max(u, m);
      x < 1 && (x = 400);
      var h = 400 / x,
        f = u * h,
        v = m * h;
      const g = a * _,
        k = i * _,
        M = r * _,
        b = c * _;
      try {
        try {
          const e = Math.max(0, f / 2 - g / 2 - 40),
            o = Math.max(0, v / 2 - (M + k) / 2 - 40),
            n = Math.max(0, f - 20),
            a = Math.max(0, v - 20);
          t.attr("viewBox", `-${e} -${o} ${n} ${a}`);
        } catch (t) {
          console.error('it"s just ViewBox error!');
        }
        !(function (n, a, i, r) {
          e.clear();
          var c = r * Math.cos((Math.PI / 180) * 45);
          const l = 0,
            d = c / 2,
            _ = d + a,
            u = t.gradient(
              "l(0, 0, 1, 1)rgba(153, 192, 255, 0.7)-rgba(204, 230, 255, 0.7)"
            ),
            p = t.gradient(
              "l(0, 0, 1, 1)rgba(153, 192, 255, 0.5)-rgba(204, 230, 255, 0.5)"
            ),
            m = t.gradient(
              "l(0, 0, 1, 1)rgba(179, 209, 255, 0.5)-rgba(230, 242, 255, 0.5)"
            ),
            x = t.gradient(
              "l(0, 0, 1, 1)rgba(179, 209, 255, 0.5)-rgba(230, 242, 255, 0.5)"
            ),
            h = t.gradient(
              "l(0, 0, 1, 1)rgba(204, 230, 255, 0.5)-rgba(242, 249, 255, 0.5)"
            );
          function f(t, e) {
            const o = parseInt(t.slice(1), 16),
              n = Math.round(2.55 * e),
              a = (o >> 16) + n,
              i = ((o >> 8) & 255) + n,
              r = (255 & o) + n;
            return `#${(
              16777216 +
              65536 * (a < 255 ? (a < 1 ? 0 : a) : 255) +
              256 * (i < 255 ? (i < 1 ? 0 : i) : 255) +
              (r < 255 ? (r < 1 ? 0 : r) : 255)
            )
              .toString(16)
              .slice(1)
              .toUpperCase()}`;
          }
          function v(t) {
            return `l(0, 0, 1, 1)${t}-${f(
              "#000000" === t ? "#a2a2a2" : t,
              -20
            )}`;
          }
          const $ = t.gradient(v(s)),
            g = t.gradient(v(s)),
            k = t.gradient(v(f(s, -10))),
            M = t.gradient(v(f(s, -10))),
            b = t.gradient(v(f(s, -30))),
            w = t.gradient(v(f(s, -15)));
          function y(a, i, c, s, d, _) {
            e.add(
              t
                .path(
                  `M${l + r / 2},${a - r / 2} L${l + n + r / 2},${a - r / 2} L${
                    l + n + r / 2
                  },${a + i - r / 2} L${l + r / 2},${a + i - r / 2} Z`
                )
                .attr({ fill: c.back, stroke: d })
            ),
              e.add(
                t
                  .path(
                    `M${l},${a} L${l + r / 2},${a - r / 2} L${l + r / 2},${
                      a + i - r / 2
                    } L${l},${a + i} Z`
                  )
                  .attr({ fill: c.left, stroke: d })
              ),
              (!_ && s) ||
                e.add(
                  t
                    .path(
                      `M${l + n},${a} L${l + n + r / 2},${a - r / 2} L${
                        l + n + r / 2
                      },${a + i - r / 2} L${l + n},${a + i} Z`
                    )
                    .attr({ fill: c.right, stroke: d })
                ),
              e.add(t.rect(l, a, n, i).attr({ fill: c.front, stroke: d })),
              s &&
                6 === o.faces &&
                (e
                  .circle(l + 8, a + i - 8, 5)
                  .attr({ fill: "white", stroke: "gray", strokeWidth: 1 }),
                e
                  .circle(l + n - 8, a + i - 8, 5)
                  .attr({ fill: "white", stroke: "gray", strokeWidth: 1 }),
                e
                  .circle(l + 8, a + 8, 5)
                  .attr({ fill: "white", stroke: "gray", strokeWidth: 1 }),
                e
                  .circle(l + n - 8, a + 8, 5)
                  .attr({ fill: "white", stroke: "gray", strokeWidth: 1 })),
              e.add(
                t
                  .path(
                    `M${l},${a} L${l + r / 2},${a - r / 2} L${l + n + r / 2},${
                      a - r / 2
                    } L${l + n},${a} Z`
                  )
                  .attr({ fill: c.bottom, stroke: "none" })
              );
          }
          "lightblue" === o.scoleColor
            ? y(
                _,
                i,
                { back: u, bottom: p, left: m, right: x, front: h },
                !1,
                "lightblue",
                !1
              )
            : y(
                _,
                i,
                { back: $, bottom: g, left: k, right: M, front: b, top: w },
                !1,
                "none",
                !1
              );
          y(
            d,
            a,
            { back: u, bottom: p, left: m, right: x, front: h },
            !0,
            "lightblue",
            4 !== o.faces
          );
        })(g, k, M, b),
          (function (e, n, a, i, r, c, s, l) {
            var d = i * Math.cos((Math.PI / 180) * 45),
              _ = i * Math.sin((Math.PI / 180) * 45);
            const u = 0,
              p = d / 2,
              m = p + a;
            t.line(u, m + n + 20, u + e, m + n + 20).attr(z()),
              t.line(u - 20, p, u - 20, p + n).attr(z()),
              o.base && t.line(u - 20, p + n, u - 20, m + n).attr(z());
            t
              .line(
                u + e,
                m + n + 20,
                u + e + _ / 2 + 20,
                m + n + 20 - d / 2 - 20
              )
              .attr(z()),
              O("L : ", `${Math.round(r)} mm`, u + e / 2, m + n + 38, N),
              O(
                "H.B : ",
                `${Math.round(c)} mm`,
                u - 25,
                p + n / 2,
                N,
                "vertical",
                u - 25,
                p + n / 2
              ),
              o.base &&
                O(
                  "H.S: ",
                  `${Math.round(s)} mm`,
                  u - 25,
                  p + n + a / 2,
                  N,
                  "vertical",
                  u - 25,
                  p + n + a / 2
                );
            O(
              "P : ",
              `${Math.round(l)} mm`,
              u + e + 20,
              m + n + 20,
              N,
              "depth",
              u + e,
              m + n - 20
            );
          })(g, k, M, b, a, i, r, c),
          n || D();
      } catch (t) {
        console.info(t);
      }
      W(), "undefined" != typeof updateTotale && updateTotale();
    }
    function z(e = 4) {
      const { arrowStart: o, arrowEnd: n } = (function (e = 4) {
        const o = e,
          n = 3,
          a = t
            .path(`M0,0 L${o},${o / 2} L0,${o} L${o / 3},${o / 2} Z`)
            .attr({ fill: "#f00" })
            .marker(0, 0, o, o, n, o / 2),
          i = t
            .path(`M${o},0 L0,${o / 2} L${o},${o} L${o - o / 3},${o / 2} Z`)
            .attr({ fill: "#f00" })
            .marker(0, 0, o, o, o - n, o / 2);
        return { arrowStart: i, arrowEnd: a };
      })(e);
      return 3 == e
        ? {
            stroke: "#37d802",
            strokeWidth: 1,
            markerStart: o,
            markerEnd: n,
            strokeDasharray: "5, 5",
          }
        : 2 == e
        ? {
            stroke: "#59D2FE",
            strokeWidth: 1,
            markerStart: o,
            markerEnd: n,
            strokeDasharray: "1, 2",
          }
        : { stroke: "#D0346C", strokeWidth: 1, markerStart: o, markerEnd: n };
    }
    function N() {
      var t = "8px";
      return (
        "undefined" != typeof DefineCubeShapeToDraw && (t = "11px"),
        { "font-size": t, "text-anchor": "middle", fill: "#043781" }
      );
    }
    function O(t, o, n, a, i, r = "horizontal", c = 0, s = 0) {
      const l = e.text(n, a, [t, o]).attr(i()),
        d =
          (l.select("tspan:nth-child(1)"),
          l.select("tspan:nth-child(2)").attr({ "font-weight": "bold" }),
          l.getBBox()),
        _ = e.rect(d.x, d.y, d.width, d.height + 2).attr({ fill: "white" });
      l.before(_),
        "vertical" === r
          ? (l.transform(`rotate(-90 ${c} ${s})`),
            _.transform(`rotate(-90 ${c} ${s})`))
          : "depth" === r &&
            (l.transform(`rotate(-45 ${c} ${s})`),
            _.transform(`rotate(-45 ${c} ${s})`));
    }
    function W() {
      o.on ||
        ($("#diameter_de_decoupe_price").val(parseFloat(p).toFixed(2)),
        $("#diameter_de_decoupe_price2").val(parseFloat(m).toFixed(2)),
        $("#p_d_d_map_1").text(`${p.toFixed(2)} + ${m.toFixed(2)} mm`));
      var t = 0,
        e = 0,
        n = document.getElementById("product_thickness");
      n && (t = parseFloat(n.value));
      var a = document.getElementById("product_density");
      a && (e = 1e3 * parseFloat(a.value));
      var i = d / 1e3,
        r = _ / 1e3,
        c = t / 1e3,
        s = 0,
        l = 0,
        u = 0,
        x = 0;
      if (o.on) {
        4 === o.faces
          ? ((s =
              o.vitrineDepth * o.vitrineHeight * 2 +
              o.vitrineWidth * o.vitrineDepth +
              o.vitrineHeight * o.vitrineWidth),
            (u = o.vitrineDepth + 2 * o.vitrineHeight + 2 * o.vitrineWidth))
          : ((s =
              o.vitrineWidth * o.vitrineHeight * 2 +
              o.vitrineHeight * o.vitrineDepth * 2 +
              o.vitrineWidth * o.vitrineDepth),
            (u =
              2 * o.vitrineDepth + 4 * o.vitrineHeight + 2 * o.vitrineWidth));
        var h = 0;
        const t = o.socleHeight / 1e3;
        o.base &&
          ((h =
            o.vitrineWidth * t * 2 +
            t * o.vitrineDepth * 2 +
            o.vitrineWidth * o.vitrineDepth),
          (x = 2 * o.vitrineWidth + 2 * o.vitrineDepth + 4 * t)),
          $("#cube_second_surface").val(h),
          (l = o.vitrineWidth * (o.vitrineHeight + t) * o.vitrineDepth);
      } else l = (s = i * r) * c;
      var f = l * e;
      return (
        $("#product_weight").val(f.toFixed(4)),
        $("#product_width").val(d),
        $("#product_height").val(_),
        $("#product_depth").val(t),
        $("#product_volume").val(l.toFixed(5)),
        $("#product_surface").val(s.toFixed(3)),
        $("#cube_diameter_socle").val(x.toFixed(3)),
        $("#cube_diameter_capot").val(u.toFixed(3)),
        $("#resume_tr_poids .option_title").text(
          f ? f.toFixed(4) + " kg" : "0 kg"
        ),
        $("#resume_tr_volume .option_title").text(
          l ? l.toFixed(5) + " m³" : "0 m³"
        ),
        $("#resume_tr_surface .option_title").text(
          s ? s.toFixed(3) + " m²" : "0 m²"
        ),
        f
      );
    }
    return {
      init: function () {
        B(),
          (function () {
            function t() {
              const t = $("#svgContainer"),
                e = $("#actualSvg");
              t.css("cursor", "grab");
              let o,
                n,
                a = !1,
                i = 0,
                r = 0,
                c = 1;
              function s() {
                const t = e.css("transform");
                if ("none" !== t) {
                  const e = t.replace(/[^0-9\-.,]/g, "").split(",");
                  if (e.length >= 6) return parseFloat(e[0]);
                }
                return 1;
              }
              function l(l) {
                (a = !0), (o = l.pageX), (n = l.pageY);
                const d = e
                  .css("transform")
                  .replace(/[^0-9\-.,]/g, "")
                  .split(",");
                d.length >= 6
                  ? ((i = parseFloat(d[4])), (r = parseFloat(d[5])))
                  : ((i = 0), (r = 0)),
                  (c = s()),
                  t.css("cursor", "grabbing"),
                  l.preventDefault();
              }
              function d(t) {
                if (a) {
                  const a = t.pageX - o,
                    s = t.pageY - n;
                  e.css(
                    "transform",
                    `translate(${i + a}px, ${r + s}px) scale(${c})`
                  );
                }
              }
              function _() {
                a && ((a = !1), t.css("cursor", "grab"));
              }
              t.on("mousedown", l),
                $(document).on("mousemove", d),
                $(document).on("mouseup", _),
                t.css("cursor", "grab");
            }
            function e() {
              $("#step_title_2 .collapse").removeClass("collapse"),
                $("#step_title_2 .collapse.in").removeClass("collapse in");
            }
            function o() {
              $("#resume_prix_de_decoupe_price").val("0"),
                $("#diameter_de_decoupe_price").val("0"),
                $("#diameter_de_decoupe_price2").val("0"),
                $("#resume_price_from_cube").val("0"),
                $("#idxr_is_rectangle").val("false"),
                $("#idxr_is_rectangle_polissage").val("false"),
                $("#idxr_is_predecoupe").val("false"),
                $("#idxr_prix_de_predecoupe").val("0");
            }
            function c(t) {
              const o = $("#fieldsHolderStepOne");
              return (
                o.length > 0 &&
                  o.find(".measurements-selector__error").remove(),
                document.getElementById("TwoDImageThumb") &&
                  document.getElementById("TwoDImageThumb").click(),
                function () {
                  (n.type = t), e(), F(n.type, 1), b(1);
                }
              );
            }
            function s(t) {
              return function () {
                (a.type = t), F(a.type, 2), b(2);
              };
            }
            function l(t) {
              return function () {
                (i.type = t), e(), F(i.type, 3), b(3);
              };
            }
            C(),
              t(),
              $("#card_2_3").on("click", function () {
                window.open("/contact-plexi-cindar", "_blank");
              }),
              $("#card_52_0").on("click", function () {
                $("#idxr_is_rectangle_polissage").val("false").change();
              }),
              $("#card_52_1").on("click", function () {
                $("#idxr_is_rectangle_polissage").val("true").change();
              }),
              $(" #card_2_1, #card_2_2, #card_2_3").on("click", function () {
                $("#idxr_is_rectangle").val("false").change();
              }),
              $("#card_2_0").on("click", function () {
                $("#idxr_is_rectangle").val("true").change();
              }),
              $("#card_17_0").on("click", function () {
                g.forEach((t) => {
                  const e = t.split("_")[1];
                  e && I(e);
                });
              }),
              $("#card_29_0").on("click", function () {
                M.forEach((t) => {
                  const e = t.split("_")[1];
                  e && I(e);
                });
              }),
              $("#card_61_1").on("click", function () {
                $("#idxr_is_predecoupe").val("true"),
                  setTimeout(function () {
                    $("#step_content_62 .card-header a").click(),
                      $("#step_content_94 .card-header a").click(),
                      $("#step_content_96 .card-header a").click(),
                      $("#step_content_97 .card-header a").click(),
                      $("#step_content_117 .card-header a").click(),
                      $("#step_content_98 .card-header a").click(),
                      $("#step_content_99 .card-header a").click(),
                      $("#step_content_100 .card-header a").click(),
                      $("#step_content_101 .card-header a").click(),
                      $("#step_content_102 .card-header a").click(),
                      $("#step_content_103 .card-header a").click(),
                      $("#step_content_104 .card-header a").click();
                  }, 500);
              }),
              $("#card_61_0").on("click", function () {
                o(),
                  $("#product_surface").val(0),
                  setTimeout(function () {
                    $("#step_content_2 .card-header a").click();
                  }, 500);
              }),
              $("#card_29_1").click(function () {
                I(31), j();
              }),
              $("#card_2_1").click(function () {
                I(5);
              }),
              $(".card-header a").on("click", function () {
                e();
              }),
              $(".qwerty-switch-wrapper").on("click", function () {
                $(".activeDemensions").toggle(), $(this).toggleClass("active");
              });
            const d = [
                { selector: "#card_17_0", value: 0 },
                { selector: "#card_17_1", value: 1 },
                { selector: "#card_17_2", value: 2 },
                { selector: "#card_17_3", value: 3 },
              ],
              _ = [
                { selector: "#card_29_0", value: 0 },
                { selector: "#card_31_0", value: 1 },
                { selector: "#card_31_1", value: 2 },
                { selector: "#card_31_12", value: 3 },
                { selector: "#card_31_2", value: 4 },
                { selector: "#card_31_3", value: 5 },
                { selector: "#card_31_13", value: 6 },
                { selector: "#card_31_4", value: 7 },
                { selector: "#card_31_5", value: 8 },
                { selector: "#card_31_6", value: 9 },
                { selector: "#card_31_14", value: 10 },
                { selector: "#card_31_8", value: 11 },
                { selector: "#card_31_9", value: 12 },
                { selector: "#card_31_11", value: 13 },
              ];
            function u() {
              [
                {
                  id_component: 62,
                  dimensions: [
                    [2050, 1550, 86.2],
                    [1550, 1025, 42.83],
                  ],
                },
                {
                  id_component: 94,
                  dimensions: [
                    [3050, 2030, 260.02],
                    [2030, 2030, 173.08],
                    [2030, 1525, 130.03],
                    [2030, 1015, 86.52],
                    [1015, 1015, 43.26],
                  ],
                },
                {
                  id_component: 96,
                  dimensions: [
                    [3050, 2050, 220.95],
                    [2050, 1525, 110.65],
                  ],
                },
                {
                  id_component: 97,
                  dimensions: [
                    [2050, 1550, 86.2],
                    [1550, 1025, 42.83],
                  ],
                },
                {
                  id_component: 98,
                  dimensions: [
                    [3050, 2050, 184.58],
                    [2050, 1525, 92.32],
                    [2050, 1015, 61.43],
                    [1020, 1015, 30.57],
                  ],
                },
                {
                  id_component: 99,
                  dimensions: [
                    [2050, 1250, 42.92],
                    [1250, 1025, 21.47],
                  ],
                },
                {
                  id_component: 100,
                  dimensions: [
                    [3050, 2050, 167.25],
                    [2050, 1525, 83.65],
                  ],
                },
                {
                  id_component: 117,
                  dimensions: [
                    [3050, 2030, 167.25],
                    [2030, 1525, 83.65],
                  ],
                },
                {
                  id_component: 101,
                  dimensions: [
                    [3050, 2030, 68.93],
                    [2030, 1525, 34.41],
                    [2440, 1220, 33.07],
                    [3050, 1560, 52.9],
                    [3050, 1220, 41.43],
                  ],
                },
                {
                  id_component: 102,
                  dimensions: [
                    [3050, 2030, 359.37],
                    [3050, 1530, 270.54],
                    [2030, 1525, 179.39],
                  ],
                },
                {
                  id_component: 103,
                  dimensions: [
                    [3e3, 2e3, 1347.48],
                    [2e3, 1500, 673.74],
                    [2e3, 1e3, 449.16],
                  ],
                },
                { id_component: 104, dimensions: [[1200, 2e3, 831.54]] },
              ].forEach((t) => {
                const { id_component: o, dimensions: a } = t;
                a.forEach((t, a) => {
                  const i = `#card_${o}_${a}`,
                    s = $(i);
                  s.length > 0
                    ? s.on("click", function () {
                        const [o, a, i] = t;
                        (r.width = Math.min(o, a)),
                          (r.height = Math.max(o, a)),
                          $("#idxr_prix_de_predecoupe").val(i),
                          c(15),
                          $("#TwoDImageThumb").click(),
                          (n.type = 15),
                          e(),
                          F(n.type, 1),
                          b(1);
                      })
                    : console.warn(`Card not found: ${i}`);
                });
              });
            }
            [
              { selector: "#card_2_0", value: 1 },
              { selector: "#card_5_1", value: 2 },
              { selector: "#card_5_12", value: 3 },
              { selector: "#card_5_2", value: 4 },
              { selector: "#card_5_3", value: 5 },
              { selector: "#card_5_14", value: 6 },
              { selector: "#card_5_4", value: 7 },
              { selector: "#card_5_5", value: 8 },
              { selector: "#card_5_6", value: 9 },
              { selector: "#card_5_15", value: 10 },
              { selector: "#card_5_8", value: 11 },
              { selector: "#card_5_11", value: 13 },
              { selector: "#card_5_13", value: 12 },
              { selector: "#card_2_2", value: 14 },
              { selector: "#card_5_16", value: 16 },
            ].forEach((t) => {
              $(t.selector).on("click", c(t.value));
            }),
              d.forEach((t) => {
                $(t.selector).on("click", s(t.value));
              }),
              _.forEach((t) => {
                $(t.selector).on("click", l(t.value));
              }),
              u();
          })(),
          x.forEach((t) => {
            P(t.id, t.default);
          }),
          E(),
          j();
      },
      initCube: function () {
        (o.on = !0),
          B(),
          (t = Snap("#actualSvg")),
          A(!0),
          (function () {
            function t() {
              $("#card_85_0").on("click", function () {
                $("#cube_modele_de_socle").val("false"),
                  (o.socleThikness = 0.01),
                  $("#text_87").val(50),
                  $("#text_87").prop("disabled", !1);
              }),
                $("#card_85_1").on("click", function () {
                  $("#cube_modele_de_socle").val("true"),
                    $("#text_87").val(15).trigger("input"),
                    $("#text_87").prop("disabled", !0);
                }),
                $("#card_86_0").on("click", function () {
                  $("#cube_materiaux_price").val("131.23");
                }),
                $("#card_86_1").on("click", function () {
                  $("#cube_materiaux_price").val("129.23");
                }),
                $("#card_86_2").on("click", function () {
                  $("#cube_materiaux_price").val("116.60");
                }),
                $("#card_84_0").on("click", function () {
                  $("#component_step_78").css("width", "50%"), (o.base = !0);
                  var t = 1.5 * $("#text_78").val();
                  $("#text_87").val(t).trigger("input"),
                    (o.socleHeight = t),
                    I(85),
                    I(86),
                    $("#parametres_de_socle").show(),
                    A();
                }),
                $("#card_84_1").on("click", function () {
                  $("#component_step_78").css("width", "100%"),
                    $("#text_87").val(0),
                    $("#parametres_de_socle").hide(),
                    $("#cube_modele_de_socle").val("false"),
                    $("#cube_materiaux_price").val("0"),
                    (o.base = !1),
                    (o.socleHeight = 0),
                    A();
                }),
                $("#card_86_0").on("click", function () {
                  (o.scoleColor = "#000000"), A();
                }),
                $("#card_86_1").on("click", function () {
                  (o.scoleColor = "#ffffff"), A();
                }),
                $("#card_86_2").on("click", function () {
                  (o.scoleColor = "lightblue"), A();
                }),
                $("#card_107_1").on("click", function () {
                  (o.faces = 5), A();
                }),
                $("#card_107_0").on("click", function () {
                  (o.faces = 4), A();
                }),
                $("#card_93_0").on("click", function () {
                  (o.faces = 4), A();
                }),
                $("#card_93_1").on("click", function () {
                  (o.faces = 5), A();
                }),
                $("#card_93_2").on("click", function () {
                  (o.faces = 6), A();
                });
            }
            C(), t();
            var e = 200;
            ["#text_76", "#text_77", "#text_78"].forEach((t) => {
              $(t)
                .val(e)
                .trigger("input")
                .closest(".step_content")
                .addClass("finished");
            }),
              $("#text_87")
                .val(1.5 * e)
                .trigger("input")
                .closest(".step_content")
                .addClass("finished");
            const n = {
              "#card_93_0, #card_93_1, #card_93_2":
                "#step_content_84 .card-header a",
              "#card_84_0, #card_84_1": "#step_content_85 .card-header a",
              "#card_85_0, #card_85_1": "#step_content_86 .card-header a",
              "#card_107_0, #card_107_1, #card_107_2":
                "#step_content_84 .card-header a",
              "#card_86_0, #card_86_1, #card_86_2":
                "#step_content_86 .card-header a",
            };
            $.each(n, (t, e) => {
              $(t).on("click", function () {
                console.log("clicked: ", $(this).attr("id")),
                  setTimeout(function () {
                    $(e).click(), setTimeout(L, 200);
                  }, 300);
              });
            }),
              $(
                "#step_title_78, #step_title_77, #step_title_76, #step_title_87"
              ).on("show.bs.collapse hide.bs.collapse", function (t) {
                t.stopPropagation(), t.preventDefault();
              });
          })(),
          setTimeout(function () {
            $("#js_icp_next_opt_76").click(),
              $("#js_icp_next_opt_77").click(),
              $("#js_icp_next_opt_78").click(),
              $("#js_icp_next_opt_87").click();
          }, 3e3);
      },
    };
  })();
  "undefined" != typeof Snap &&
    ("undefined" != typeof DefineCubeShapeToDraw ? t.initCube() : t.init(),
    $("#resume_tr_epaisseur").remove());
}),
  document.addEventListener("keydown", function (t) {
    (123 === t.keyCode ||
      (t.ctrlKey && t.shiftKey && (73 === t.keyCode || 74 === t.keyCode)) ||
      (t.ctrlKey && 85 === t.keyCode)) &&
      t.preventDefault();
  }),
  (console.log =
    console.warn =
    console.error =
    console.info =
    console.debug =
      function () {
        return !1;
      }),
  setInterval(function () {
    const t = new Date().getTime();
    new Date().getTime() - t > 100 && (document.body.innerHTML = "");
  }, 500),
  document.addEventListener("contextmenu", function (t) {
    t.preventDefault();
  });
