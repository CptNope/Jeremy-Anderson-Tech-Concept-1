---
name: Jeremy Anderson Tech
description: Systems that hold steady. An oscilloscope-instrument identity for a founder-led engineering studio.
colors:
  ink-950: "#07090B"
  graticule: "#0A0C0F"
  panel: "#10141A"
  raised: "#161B22"
  line: "#222A34"
  line-strong: "#2E3845"
  line-control: "#5A6675"
  fg-subtle: "#7D8996"
  fg-muted: "#A3AFBC"
  ice: "#E6ECF2"
  ch1-cyan: "#3CE3F0"
  ch2-ultraviolet: "#9A8CFF"
  ch3-amber: "#FFC23D"
  ch4-signal: "#5EF0A0"
  danger: "#FF5C7A"
  paper: "#F3F5F7"
  paper-muted: "#465260"
  ch1-deep: "#007C88"
typography:
  display:
    fontFamily: "Archivo, Helvetica Neue, Arial, sans-serif"
    fontSize: "clamp(3.25rem, 1.6rem + 6vw, 6rem)"
    fontWeight: 640
    lineHeight: 0.92
    letterSpacing: "-0.035em"
    fontVariation: "'wdth' 125"
  headline:
    fontFamily: "Archivo, Helvetica Neue, Arial, sans-serif"
    fontSize: "clamp(1.875rem, 1.3rem + 2vw, 2.75rem)"
    fontWeight: 620
    lineHeight: 1.05
    letterSpacing: "-0.02em"
    fontVariation: "'wdth' 118"
  title:
    fontFamily: "Archivo, Helvetica Neue, Arial, sans-serif"
    fontSize: "1.375rem"
    fontWeight: 600
    lineHeight: 1.15
    fontVariation: "'wdth' 112"
  body:
    fontFamily: "Archivo, Helvetica Neue, Arial, sans-serif"
    fontSize: "1.0625rem"
    fontWeight: 400
    lineHeight: 1.6
  label:
    fontFamily: "Martian Mono, ui-monospace, Menlo, monospace"
    fontSize: "0.75rem"
    fontWeight: 400
    letterSpacing: "0.06em"
    fontVariation: "'wdth' 90"
rounded:
  tag: "2px"
  control: "6px"
  panel: "12px"
spacing:
  s1: "4px"
  s2: "8px"
  s3: "12px"
  s4: "16px"
  s5: "24px"
  s6: "32px"
  s7: "48px"
  s8: "64px"
  s9: "96px"
  s10: "128px"
components:
  button-primary:
    backgroundColor: "{colors.ch1-cyan}"
    textColor: "{colors.graticule}"
    rounded: "{rounded.control}"
    padding: "0 20px"
    height: "48px"
  button-primary-hover:
    backgroundColor: "#7AEDF6"
  button-secondary:
    backgroundColor: "transparent"
    textColor: "{colors.ice}"
    rounded: "{rounded.control}"
    height: "48px"
  input:
    backgroundColor: "{colors.ink-950}"
    textColor: "{colors.ice}"
    rounded: "{rounded.control}"
    height: "48px"
  channel-tag:
    backgroundColor: "transparent"
    textColor: "{colors.ice}"
    rounded: "{rounded.tag}"
    typography: "{typography.label}"
  panel:
    backgroundColor: "{colors.panel}"
    rounded: "{rounded.panel}"
---

# Design System: Jeremy Anderson Tech

## Overview

**Creative North Star: "The Calibrated Instrument"**

Every page is a modern digital oscilloscope screen. A graticule-black ground carries faint 10 × 8 measurement divisions; one beam-drawn trace arrives unsettled, triggers, and locks steady. The JAT monogram is that trace: a step response whose transient spells J, A and T. The system says one thing everywhere: this studio measures a system, then makes it hold steady.

Density is calm and editorial. Expanded Archivo headlines carry authority at large sizes, running text stays at normal width for comfort, and anything measured (prices, timelines, channel IDs, coordinates) is set in Martian Mono. Four channel colors identify service lines and never decorate. Depth comes from tone steps and hairlines; glass appears only as a bezel over a live trace.

The brief bans generic AI imagery, glowing brains, particle spheres, stock security graphics and SaaS templates. Photography documents real people and real screens; illustrations are trace diagrams of real systems.

**Key Characteristics:**
- Graticule-black ground with 10 × 8 divisions and centre-axis tick rulers on signature screens
- One trace, one lock sequence per page; canvases stop drawing when settled
- Channel colors as identifiers (CH1 cyan, CH2 ultraviolet, CH3 amber, CH4 signal)
- Expanded grotesk display, normal-width body, mono only for data
- Hairline frames, small radii, no decorative shadows

## Colors

A near-black instrument screen with ice-white readouts and four signal colors held to about 3% of any surface.

### Primary
- **Channel Cyan** (#3CE3F0): the monogram, primary buttons, links, focus rings and CH1 Platforms. Graticule text on cyan only.

### Secondary
- **Ultraviolet** (#9A8CFF): CH2 Intelligence identifier; trigger-level reference lines in diagrams.

### Tertiary
- **Amber** (#FFC23D): CH3 Security identifier and warning state.
- **Signal** (#5EF0A0): CH4 Operations identifier, success and "settled" state.

### Neutral
- **Graticule** (#0A0C0F): page ground. **Ink 950** (#07090B): screens, footer, inputs.
- **Panel** (#10141A) and **Raised** (#161B22): containers and sticky cards.
- **Line** (#222A34), **Line Strong** (#2E3845): hairline dividers and tags. **Line Control** (#5A6675): form and button borders (3.35:1).
- **Ice** (#E6ECF2): primary text. **Muted** (#A3AFBC): secondary text. **Subtle** (#7D8996): labels only.
- **Paper set** (#F3F5F7 ground, #465260 text, #007C88 deep cyan): print and light documents.

### Named Rules
**The Identifier Rule.** Channel colors label a service line or a state. They never fill large areas, never tint body text, and always travel with their CH number and name.
**The Cyan-Carries-Ink Rule.** Text on Channel Cyan is always Graticule (12.5:1).

## Typography

**Display Font:** Archivo, variable width 112–125% (with Helvetica Neue, Arial)
**Body Font:** Archivo at width 100% (with Helvetica Neue, Arial)
**Label/Mono Font:** Martian Mono at width 90% (with ui-monospace, Menlo)

**Character:** Wide, test-equipment lettering for headlines; plain, readable grotesk for prose; a compact mono for every measured value.

### Hierarchy
- **Display** (640, clamp 3.25–6rem, 0.92): hero headline and page-head H1s at width 125%.
- **Headline** (620, clamp 1.875–2.75rem, 1.05): section heads at width 118%.
- **Title** (600, 1.375rem, 1.15): component and card headings at width 112%.
- **Body** (400, 1.0625rem, 1.6): prose, max 58–70ch.
- **Label** (400, 0.75rem, +0.06em, uppercase when under ~35 characters): readouts, prices, timings, channel IDs. Longer readouts drop uppercase.

### Named Rules
**The Data-Only Mono Rule.** Martian Mono appears only when the content is data. A headline in mono is a costume.

## Layout

12-column container to 1280px with a fluid gutter (16–48px); content sections pace at 72–128px vertical padding, alternating Graticule and Ink 950 grounds. Section heads pair a headline left with a lede right on desktop and stack under 860px. Signature screens (hero, page heads, case-study plates) use the graticule; ordinary content sits on plain ground. Breakpoints: 640, 760, 860, 1000 and 1080px (navigation collapses below 1080). The 4px spacing base mirrors one minor graticule tick.

## Elevation & Depth

Flat and tonal. Surfaces step Graticule → Panel → Raised with 1px hairlines; the only shadow is a soft raised shadow on toasts and lifted panels. Glass (Panel at 62–80% with 14px blur) is reserved for the sticky header and bezels sitting over a live trace.

### Shadow Vocabulary
- **Raised** (`box-shadow: 0 10px 30px -8px rgba(0,0,0,.6)`): toasts and lifted panels only.

### Named Rules
**The Bezel Rule.** Glass exists only where a trace moves beneath it. It is never a card style.

## Shapes

Small, instrument radii: 2px tags, 6px controls, 12px panels and screens. Hairline 1px borders define everything; the monogram and all diagrams use round-capped single strokes on a 16-unit grid.

## Components

### Buttons
- **Shape:** gently squared (6px), 48px tall (44px small).
- **Primary:** Channel Cyan with Graticule text, Archivo 600 at width 108%; arrow icon advances 3px on hover.
- **Hover / Focus:** lighter cyan (#7AEDF6) on hover; 2px cyan outline with 2px offset on focus.
- **Secondary:** transparent with Line Control border, turning cyan on hover.

### Chips (channel tags)
- **Style:** 2px radius, Line Strong border, colored dot plus mono "CH1" in channel color and the channel name in Ice.

### Cards / Containers
- **Corner Style:** 12px. **Background:** Panel or Ink 950. **Border:** 1px Line. **Internal Padding:** 24px. Cards appear for work items only; services use ruled rows, not cards.

### Inputs / Fields
- **Style:** Ink 950 fill, Line Control border, 6px radius, 48px tall, cyan caret.
- **Focus:** cyan border plus a 3px 18%-cyan ring. **Error:** Danger border and an inline icon + message.

### Navigation
- Sticky glass header with the horizontal lockup; links in Ice-muted with a 2px cyan underline for the current page; one primary "Book a call" button. Below 1080px a menu button opens a full-width Ink 950 panel.

### Scope trace (signature)
Canvas trace that sweeps noise three times, triggers, and burns in the JAT mark at logo weight, with centre-axis ticks and pointer measurement cursors. Inner pages draw a channel waveform instead (step, burst, pulse, sine). Reduced motion renders the locked frame immediately.

### Channel strip and rows
Four-column readout strip (2 × 2 on phones) linking to channels; channel rows pair ID, promise, services and entry price on a ruled grid.

## Do's and Don'ts

### Do:
- **Do** draw systems as single-stroke trace diagrams on a graticule, labeled in Martian Mono.
- **Do** keep channel color to identifiers with their CH number and name.
- **Do** publish prices and timings in mono readouts.
- **Do** stop canvases once the trace has locked.

### Don't:
- **Don't** add glows, bloom or gradients to the monogram or traces.
- **Don't** use glowing brains, particle spheres, padlocks, binary rain or AI-generated people.
- **Don't** place kicker labels above headings; channel IDs sit inline with names.
- **Don't** use glass as a card style or monospace for headlines.
