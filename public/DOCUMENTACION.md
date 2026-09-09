
---

## 📄 Archivo: `DOCUMENTACION.md`

```markdown
# Documentación Técnica - CMV Asesoría y Capacitación México

## 1. Descripción general
Sitio web estático alojado en GitHub Pages. Su objetivo es mostrar servicios, cursos STPS y permitir contacto vía correo electrónico, respetando la política de privacidad del cliente.

**URL del sitio:** https://cmv-asesoria-y-capacitacion-mexico.github.io/CMV-ASESORIA-MEXICO/

**Desarrolladora:** Gisela Cruz Anastacio

---

## 2. Estructura de archivos recomendada

---

## 3. Componentes clave del sitio

### 3.1 Hero (cabecera principal)
- **Texto principal:** "Más de 150 Cursos STPS para la Industria"
- **Subtítulo:** "Somos una empresa especializada en la consultoría y formación..."

### 3.2 Sección de Servicios (4 bloques)

| Servicio | Descripción |
|----------|-------------|
| Consultoría y Formación | Integración de conocimientos y habilidades |
| Asesoría de Negocios | Identificación de áreas de oportunidad |
| Capacitación Virtual | Cursos en entorno virtual sin viajes |
| Formación Presencial | Capacitación en sitio para diversos sectores |

### 3.3 Catálogo de cursos (muestra de 12 cursos)

Los cursos mostrados en el sitio son:

1. Trabajos en Alturas
2. NOM-035-STPS
3. Manejo de Montacargas
4. Manejo de Extintores
5. Bloqueo y Etiquetado
6. Operación Segura del Cargador Frontal
7. Operación Segura del Minicargador (BOBCAT)
8. Trabajo en Espacios Confinados y Rescate Industrial
9. Brigadas Contraincendios
10. Delimitación de Áreas de Riesgo
11. Brigadas de Búsqueda y Rescate
12. Brigadas de Contención de Derrames

**Nota importante:** El cliente ofrece **más de 150 cursos STPS** en total. El catálogo mostrado es una **muestra representativa**.

### 3.4 Botón "Ver Catálogo"
- **Ubicación:** Después de la lista de cursos
- **Comportamiento esperado:** Debe abrir el catálogo completo (idealmente un PDF o una página secundaria con todos los cursos)

### 3.5 Sección "¿Quiénes Somos?"

**Misión:**
> Brindar a nuestros clientes capacitaciones de calidad para formar personas competentes, capaces de aportar al crecimiento de su empresa y el país, mediante liderazgo y responsabilidad.

**Visión:**
> Ser en el ámbito nacional, los colaboradores de referencia de nuestros clientes, reconocidos por ofertar capacitación vanguardista e integral.

**Ventajas (¿Por qué elegirnos?):**
- Profesionalismo
- Experiencia
- Flexibilidad
- Ambiente de Aprendizaje

### 3.6 Footer (parte inferior)
- **Ubicación:** Al final del `<body>`
- **Contenido:** Información legal, derechos reservados, año actual
- **Importante:** No contiene números telefónicos por política de privacidad del cliente

### 3.7 Botón flotante de contacto por Gmail
- **Ubicación:** Esquina inferior derecha, posición fija (`position: fixed`)
- **Comportamiento:** Al hacer clic → se abre `mailto:correo@cmv.com` (cliente de correo predeterminado)
- **Razón de negocio:** El cliente **no autoriza** mostrar número telefónico personal por privacidad y seguridad

**Código de ejemplo:**

```html
<a href="mailto:cmv.asesoria@ejemplo.com" class="btn-flotante-email">
  Contactar por Gmail
</a>
