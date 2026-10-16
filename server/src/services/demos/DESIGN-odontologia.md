# DESIGN.md — Demos de odontología

Especificación de la página de muestra para clínicas odontológicas colombianas.
Escrita con la skill `web-design` (spec primero, código después) y con el
conocimiento del nicho del vault: *Nicho — Odontología (para vender páginas web)*.

**Nivel de interacción: L2** (reveal al scroll, parallax suave, barra que cambia).

---

## 1. Tema visual y atmósfera

**Filosofía:** una clínica donde uno dejaría que le metieran la mano en la boca.
Confianza clínica, no frialdad de hospital.

**Palabras de atmósfera:** limpio · sereno · cercano · preciso · luminoso

**En una frase:** se siente como entrar a un consultorio bien iluminado donde
alguien te recibe por tu nombre.

**Lo que NO es:** ni tecnológico-futurista (asusta), ni pastel-infantil (no da
confianza para un implante), ni corporativo gris (no es una EPS).

---

## 2. Paleta y roles

Un verde azulado médico como base, un coral cálido como acento humano. El coral
es lo que la separa de las mil webs dentales azules.

```css
:root{
  --tinta:#0C2B2F;        --tinta-rgb:12,43,47;        /* titulares */
  --prof:#114E52;         --prof-rgb:17,78,82;         /* marca, fondos hondos */
  --agua:#2E8B94;         --agua-rgb:46,139,148;       /* interactivo */
  --menta:#D7EDEC;        --menta-rgb:215,237,236;     /* fondos suaves */
  --coral:#E8705B;        --coral-rgb:232,112,91;      /* acento, CTA secundario */
  --coral-sua:#FDEDE9;    --coral-sua-rgb:253,237,233;
  --crema:#FBFAF7;        --crema-rgb:251,250,247;     /* fondo de página */
  --blanco:#FFFFFF;       --blanco-rgb:255,255,255;
  --gris:#5B6F72;         --gris-rgb:91,111,114;       /* texto secundario */
  --borde:#E4EBEA;        --borde-rgb:228,235,234;
  --oro:#E0A82E;          --oro-rgb:224,168,46;        /* estrellas */
}
```

**Roles:** fondo `--crema` · superficie `--blanco` · texto `--tinta` ·
secundario `--gris` · interactivo `--agua` · CTA principal `--prof` ·
CTA WhatsApp verde propio de la marca de WhatsApp · acento `--coral`.

---

## 3. Tipografía

```css
@import url('https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,700&family=DM+Sans:wght@400;500;700&display=swap');
--serif:'Fraunces',Georgia,'Times New Roman',serif;
--sans:'DM Sans',-apple-system,'Segoe UI',system-ui,sans-serif;
```

**Fraunces** en titulares: una serif moderna y cálida, poco usada en salud — es
lo que hace que no parezca plantilla. **DM Sans** en todo lo demás.

| Nivel | Tamaño | Peso | Familia |
|---|---|---|---|
| H1 | `clamp(2.4rem,6vw,4.2rem)` · lh 1.05 · ls −.03em | 700 | serif |
| H2 | `clamp(1.7rem,3.6vw,2.6rem)` · lh 1.15 | 700 | serif |
| H3 | `1.08rem` | 700 | sans |
| Cuerpo | `1.02rem` · lh 1.65 | 400 | sans |
| Antetítulo | `.78rem` · ls .14em · mayúsculas | 700 | sans |
| Cifra | `clamp(2rem,4vw,3rem)` | 700 | serif |

**Prohibidas:** Montserrat, Poppins, Raleway, Comic Sans. Las tres primeras son
la firma visual de las plantillas baratas.

---

## 4. Componentes (todos los estados)

```css
.btn{font:700 .95rem/1 var(--sans);padding:16px 30px;border-radius:999px;
     display:inline-flex;align-items:center;gap:9px;min-height:48px;
     border:1.5px solid transparent;cursor:pointer;
     transition:transform .18s cubic-bezier(.2,.7,.3,1),box-shadow .18s,background .18s}
.btn-pri{background:var(--prof);color:var(--blanco)}
.btn-pri:hover{background:var(--tinta);transform:translateY(-2px);
               box-shadow:0 10px 24px rgba(var(--prof-rgb),.28)}
.btn-pri:active{transform:translateY(0);box-shadow:0 4px 10px rgba(var(--prof-rgb),.3)}
.btn-pri:focus-visible{outline:3px solid rgba(var(--coral-rgb),.55);outline-offset:3px}
.btn-pri:disabled{background:var(--borde);color:var(--gris);cursor:not-allowed;transform:none}

.btn-wa{background:#25D366;color:#06281A}
.btn-wa:hover{filter:brightness(1.06);transform:translateY(-2px)}
.btn-gho{background:transparent;color:var(--prof);border-color:rgba(var(--prof-rgb),.3)}
.btn-gho:hover{border-color:var(--prof);background:rgba(var(--prof-rgb),.05)}

.card{background:var(--blanco);border:1px solid var(--borde);border-radius:22px;
      padding:30px;transition:transform .22s,box-shadow .22s,border-color .22s}
.card:hover{transform:translateY(-5px);box-shadow:0 18px 44px rgba(var(--tinta-rgb),.1);
            border-color:rgba(var(--agua-rgb),.4)}
.card:focus-within{border-color:var(--agua)}

a{color:var(--agua);text-decoration:none}
a:hover{color:var(--prof)}
a:focus-visible{outline:2px solid var(--agua);outline-offset:2px;border-radius:4px}

input{font:400 1rem var(--sans);padding:15px 17px;border:1.5px solid var(--borde);
      border-radius:14px;background:var(--blanco);width:100%;min-height:48px}
input:hover{border-color:rgba(var(--agua-rgb),.5)}
input:focus{outline:none;border-color:var(--agua);box-shadow:0 0 0 4px rgba(var(--agua-rgb),.14)}
input:disabled{background:var(--menta);cursor:not-allowed}
```

---

## 5. Layout

Contenedor `1160px`, respiro lateral `24px` (`20px` en móvil).
Escala de espacio: `8 · 14 · 22 · 36 · 56 · 88 · 132`.
Secciones: `clamp(64px,9vw,118px)` arriba y abajo.
Rejillas: servicios `repeat(auto-fit,minmax(270px,1fr))`.

---

## 6. Profundidad

```css
--s1:0 1px 2px rgba(var(--tinta-rgb),.05);
--s2:0 6px 18px rgba(var(--tinta-rgb),.07);
--s3:0 18px 44px rgba(var(--tinta-rgb),.1);
--s4:0 30px 70px rgba(var(--tinta-rgb),.14);
```

Tres planos: fondo crema → tarjetas blancas (`--s2`) → flotantes (`--s4`).

---

## 7. Animación e interacción (L2)

| Categoría | Dónde | Efecto |
|---|---|---|
| Texto H1 | primera pantalla | reveal por líneas con `clip-path`, 90 ms de retraso entre líneas |
| Texto H2 | cada sección | sube 22 px + aparece al entrar en pantalla |
| Texto cuerpo | párrafos y antetítulos | fade escalonado, 60 ms entre hijos |
| Elemento | botones y tarjetas | elevación al pasar el cursor, hundido al pulsar |
| Componente | servicios | luz que sigue al cursor (`--mx/--my` + radial-gradient) |
| Fondo | primera pantalla | dos manchas de color con parallax suave al hacer scroll |

Scroll reveal con `IntersectionObserver` (nada de librerías).
Barra: se vuelve sólida con sombra al pasar los 40 px.

```css
@media (prefers-reduced-motion:reduce){
  *,*::before,*::after{animation-duration:.01ms!important;transition-duration:.01ms!important}
  .reveal{opacity:1!important;transform:none!important}
}
```

---

## 8. Do's and Don'ts

**Sí:**
1. El teléfono siempre es `<a href="tel:">` y se ve como botón.
2. Botón de agendar en la barra fija, en la primera pantalla y en cada servicio.
3. Las reseñas reales de Google, con el número exacto, arriba del pliegue.
4. Nombrar la ciudad y el barrio: el dueño tiene que reconocerse.
5. Formulario de máximo 3 campos.

**No:**
1. **Nunca un rectángulo de color sólido donde va una foto.** Si no hay foto, se
   resuelve con composición, tipografía o una ilustración SVG propia.
2. Nunca fotos de banco con modelos extranjeros sonriendo a cámara.
3. Nunca "líderes del mercado", "excelencia", "compromiso", "calidad superior".
4. Nunca inventar años de experiencia, premios ni certificaciones.
5. Nunca prometer resultados ("sonrisa perfecta garantizada") — es salud.
6. Nunca Montserrat ni Poppins.
7. Nunca la misma paleta para dos clínicas de la misma ciudad.

---

## 9. Responsive

Cortes: `600px` y `980px`.
Táctiles mínimo `48×48px`. Cero desbordamiento horizontal bajo 600 px.
Bajo 980 px: menú colapsa a botón de WhatsApp; la primera pantalla pasa a una
columna; servicios a una columna.
En móvil las manchas de fondo se congelan (sin parallax).

---

## Los tres momentos que tienen que impresionar

1. **Primera pantalla:** el titular aparece línea por línea con máscara, sobre
   un fondo con dos manchas de color que se mueven despacio. La prueba de las
   431 reseñas entra después, desde abajo.
2. **Primer scroll:** franja oscura con las cifras del negocio contando desde
   cero al entrar en pantalla — sus reseñas, su calificación, sus años en la
   ciudad.
3. **Servicios:** no es una rejilla uniforme. La primera tarjeta ocupa dos
   columnas, y todas tienen una luz que sigue al cursor.

**El detalle con gracia:** la barra de arriba muestra *"Abierto ahora"* en verde
si son horas de atención según el horario real de Google, y *"Abrimos mañana a
las 8:30"* si no. Calculado en el navegador, sin servidor.
