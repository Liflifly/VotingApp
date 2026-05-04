<template>
  <Teleport to="body">
    <Transition name="crop-fade">
      <div v-if="show" class="neo-crop-overlay" @mousedown.self="$emit('update:show', false)">
        <div class="neo-crop-card">

          <!-- Header -->
          <div class="neo-crop-header">
            <div class="flex items-center gap-2">
              <span class="material-symbols-outlined text-neo-yellow">crop</span>
              <span class="font-heading font-black text-sm uppercase tracking-widest text-white">Sesuaikan Foto</span>
            </div>
            <button @click="close" class="text-gray-400 hover:text-white transition-colors cursor-pointer">
              <span class="material-symbols-outlined text-2xl">close</span>
            </button>
          </div>

          <!-- Canvas Area -->
          <div class="neo-crop-canvas-wrap" ref="wrapRef">
            <canvas
              ref="canvasRef"
              @mousedown="onPointerDown"
              @mousemove="onPointerMove"
              @mouseup="onPointerUp"
              @mouseleave="onPointerUp"
              @wheel.prevent="onWheel"
              @touchstart.prevent="onTouchStart"
              @touchmove.prevent="onTouchMove"
              @touchend="onTouchEnd"
            ></canvas>
          </div>

          <!-- Error -->
          <div v-if="errorMsg" class="mx-4 mt-2 p-2 bg-red-900/40 border border-red-500 text-red-400 font-heading text-xs uppercase">
            {{ errorMsg }}
          </div>

          <!-- Hint -->
          <p class="text-center text-zinc-500 text-xs font-body py-2 px-4">
            Geser &amp; scroll untuk menyesuaikan posisi
          </p>

          <!-- Zoom Slider -->
          <div class="neo-crop-controls">
            <span class="material-symbols-outlined text-zinc-400 text-lg">zoom_out</span>
            <input
              type="range" min="0" max="100" step="1"
              v-model.number="zoomPercent"
              @input="onSliderChange"
              class="neo-crop-slider"
            />
            <span class="material-symbols-outlined text-zinc-400 text-lg">zoom_in</span>
          </div>

          <!-- Actions -->
          <div class="neo-crop-actions">
            <button @click="close" class="neo-crop-btn-cancel">
              <span class="material-symbols-outlined text-base">close</span> Batal
            </button>
            <button @click="doCrop" :disabled="processing" class="neo-crop-btn-confirm">
              <span v-if="processing" class="material-symbols-outlined text-base neo-spin">progress_activity</span>
              <span v-else class="material-symbols-outlined text-base">check</span>
              {{ processing ? 'Memproses...' : 'Simpan Foto' }}
            </button>
          </div>

        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, watch, nextTick, onUnmounted } from 'vue';

const props = defineProps({
  show:          { type: Boolean, default: false },
  imageSrc:      { type: String,  default: null },
  cropShape:     { type: String,  default: 'circle' },   // 'circle' | 'square'
  outputSize:    { type: Number,  default: 512 },
  outputQuality: { type: Number,  default: 0.92 },
});

const emit = defineEmits(['update:show', 'crop']);

// ── Refs ──────────────────────────────────────────────────────────────────────
const wrapRef     = ref(null);
const canvasRef   = ref(null);
const errorMsg    = ref(null);
const processing  = ref(false);
const zoomPercent = ref(0);

// ── Internal state ────────────────────────────────────────────────────────────
let img       = null;   // HTMLImageElement
let ctx       = null;   // CanvasRenderingContext2D
let canvasSize = 0;     // px (square)

let offsetX   = 0;
let offsetY   = 0;
let scale     = 1;
let minScale  = 1;
let maxScale  = 4;

// Drag state
let dragging  = false;
let dragStartX = 0;
let dragStartY = 0;
let dragOffsetX = 0;
let dragOffsetY = 0;

// Pinch state
let lastPinchDist = 0;

const ZOOM_MULT = 4;

// ── Watch show → init ─────────────────────────────────────────────────────────
watch(() => props.show, async (val) => {
  if (val && props.imageSrc) {
    errorMsg.value = null;
    processing.value = false;
    zoomPercent.value = 0;
    await nextTick();
    // Extra frame to ensure DOM is painted
    requestAnimationFrame(() => {
      requestAnimationFrame(() => {
        loadImage();
      });
    });
  }
});

// ── Load image ────────────────────────────────────────────────────────────────
function loadImage() {
  const wrap = wrapRef.value;
  const canvas = canvasRef.value;
  if (!wrap || !canvas) return;

  canvasSize = wrap.clientWidth;
  canvas.width  = canvasSize;
  canvas.height = canvasSize;
  ctx = canvas.getContext('2d');

  img = new Image();
  img.onload = () => {
    // Calculate fit scale — image must COVER the canvas (fill, not fit)
    const scaleX = canvasSize / img.naturalWidth;
    const scaleY = canvasSize / img.naturalHeight;
    minScale = Math.max(scaleX, scaleY);
    maxScale = minScale * ZOOM_MULT;
    scale = minScale;

    // Center the image
    offsetX = (canvasSize - img.naturalWidth * scale) / 2;
    offsetY = (canvasSize - img.naturalHeight * scale) / 2;

    zoomPercent.value = 0;
    draw();
  };
  img.onerror = () => {
    errorMsg.value = 'Gagal memuat gambar. Coba file lain.';
  };
  img.src = props.imageSrc;
}

// ── Draw frame ────────────────────────────────────────────────────────────────
function draw() {
  if (!ctx || !img) return;
  const s = canvasSize;

  // Clear
  ctx.clearRect(0, 0, s, s);

  // Draw scaled image
  ctx.drawImage(
    img,
    offsetX, offsetY,
    img.naturalWidth * scale,
    img.naturalHeight * scale
  );

  // Dark overlay mask
  ctx.save();

  if (props.cropShape === 'circle') {
    // Draw dark overlay everywhere, then cut out a circle
    ctx.beginPath();
    ctx.rect(0, 0, s, s);
    ctx.arc(s / 2, s / 2, s / 2, 0, Math.PI * 2, true);
    ctx.closePath();
    ctx.fillStyle = 'rgba(0, 0, 0, 0.65)';
    ctx.fill();

    // Circle border
    ctx.beginPath();
    ctx.arc(s / 2, s / 2, s / 2 - 1, 0, Math.PI * 2);
    ctx.strokeStyle = 'rgba(255, 255, 255, 0.5)';
    ctx.lineWidth = 2;
    ctx.stroke();
  } else {
    // Square crop — add subtle corner markers instead of full overlay
    // Draw thin border around entire canvas
    ctx.strokeStyle = 'rgba(255, 255, 255, 0.5)';
    ctx.lineWidth = 2;
    ctx.strokeRect(1, 1, s - 2, s - 2);

    // Corner brackets
    const corner = 24;
    ctx.strokeStyle = 'rgba(255, 255, 255, 0.9)';
    ctx.lineWidth = 3;

    // Top-left
    ctx.beginPath();
    ctx.moveTo(1, corner); ctx.lineTo(1, 1); ctx.lineTo(corner, 1);
    ctx.stroke();
    // Top-right
    ctx.beginPath();
    ctx.moveTo(s - corner, 1); ctx.lineTo(s - 1, 1); ctx.lineTo(s - 1, corner);
    ctx.stroke();
    // Bottom-left
    ctx.beginPath();
    ctx.moveTo(1, s - corner); ctx.lineTo(1, s - 1); ctx.lineTo(corner, s - 1);
    ctx.stroke();
    // Bottom-right
    ctx.beginPath();
    ctx.moveTo(s - corner, s - 1); ctx.lineTo(s - 1, s - 1); ctx.lineTo(s - 1, s - corner);
    ctx.stroke();
  }

  ctx.restore();
}

// ── Clamp offsets ─────────────────────────────────────────────────────────────
function clampOffset() {
  const imgW = img.naturalWidth * scale;
  const imgH = img.naturalHeight * scale;

  // Image must cover the entire canvas — no gap allowed
  // Max offset (image left/top edge can't go past canvas left/top)
  const maxOX = 0;
  const maxOY = 0;
  // Min offset (image right/bottom edge can't go before canvas right/bottom)
  const minOX = canvasSize - imgW;
  const minOY = canvasSize - imgH;

  offsetX = Math.min(maxOX, Math.max(minOX, offsetX));
  offsetY = Math.min(maxOY, Math.max(minOY, offsetY));
}

// ── Mouse drag ────────────────────────────────────────────────────────────────
function onPointerDown(e) {
  dragging = true;
  dragStartX = e.clientX;
  dragStartY = e.clientY;
  dragOffsetX = offsetX;
  dragOffsetY = offsetY;
  e.target.style.cursor = 'grabbing';
}

function onPointerMove(e) {
  if (!dragging) return;
  offsetX = dragOffsetX + (e.clientX - dragStartX);
  offsetY = dragOffsetY + (e.clientY - dragStartY);
  clampOffset();
  draw();
}

function onPointerUp(e) {
  dragging = false;
  if (e.target) e.target.style.cursor = 'grab';
}

// ── Wheel zoom ────────────────────────────────────────────────────────────────
function onWheel(e) {
  if (!img) return;
  const delta = e.deltaY > 0 ? -0.04 : 0.04;
  zoomBy(delta, e.offsetX, e.offsetY);
}

function zoomBy(delta, pivotX, pivotY) {
  const oldScale = scale;
  scale = Math.min(maxScale, Math.max(minScale, scale + delta * (maxScale - minScale)));

  // Zoom towards pivot point
  const ratio = scale / oldScale;
  offsetX = pivotX - (pivotX - offsetX) * ratio;
  offsetY = pivotY - (pivotY - offsetY) * ratio;

  clampOffset();
  syncSlider();
  draw();
}

function syncSlider() {
  const pct = ((scale - minScale) / (maxScale - minScale)) * 100;
  zoomPercent.value = Math.round(Math.min(100, Math.max(0, pct)));
}

// ── Slider → zoom ─────────────────────────────────────────────────────────────
function onSliderChange() {
  if (!img) return;
  const oldScale = scale;
  scale = minScale + (zoomPercent.value / 100) * (maxScale - minScale);

  // Zoom towards center
  const cx = canvasSize / 2;
  const cy = canvasSize / 2;
  const ratio = scale / oldScale;
  offsetX = cx - (cx - offsetX) * ratio;
  offsetY = cy - (cy - offsetY) * ratio;

  clampOffset();
  draw();
}

// ── Touch (drag + pinch) ──────────────────────────────────────────────────────
function onTouchStart(e) {
  if (e.touches.length === 1) {
    dragging = true;
    dragStartX = e.touches[0].clientX;
    dragStartY = e.touches[0].clientY;
    dragOffsetX = offsetX;
    dragOffsetY = offsetY;
  } else if (e.touches.length === 2) {
    dragging = false;
    lastPinchDist = pinchDist(e);
  }
}

function onTouchMove(e) {
  if (e.touches.length === 1 && dragging) {
    offsetX = dragOffsetX + (e.touches[0].clientX - dragStartX);
    offsetY = dragOffsetY + (e.touches[0].clientY - dragStartY);
    clampOffset();
    draw();
  } else if (e.touches.length === 2) {
    const dist = pinchDist(e);
    const delta = (dist - lastPinchDist) * 0.003;
    lastPinchDist = dist;
    zoomBy(delta, canvasSize / 2, canvasSize / 2);
  }
}

function onTouchEnd() {
  dragging = false;
  lastPinchDist = 0;
}

function pinchDist(e) {
  const dx = e.touches[0].clientX - e.touches[1].clientX;
  const dy = e.touches[0].clientY - e.touches[1].clientY;
  return Math.sqrt(dx * dx + dy * dy);
}

// ── Crop & emit ───────────────────────────────────────────────────────────────
function doCrop() {
  if (!img || processing.value) return;
  errorMsg.value = null;
  processing.value = true;

  try {
    const outSize = props.outputSize;
    const outCanvas = document.createElement('canvas');
    outCanvas.width  = outSize;
    outCanvas.height = outSize;
    const outCtx = outCanvas.getContext('2d');

    // The visible canvas maps 1:1 to the crop area.
    // We need to figure out what portion of the original image is visible
    // and draw that at output resolution.
    const ratio = outSize / canvasSize;

    outCtx.drawImage(
      img,
      // Source: map from canvas coords back to image coords
      -offsetX / scale,
      -offsetY / scale,
      canvasSize / scale,
      canvasSize / scale,
      // Destination: fill entire output canvas
      0, 0, outSize, outSize
    );

    // If circle, clip to circle (for transparent PNG — but we output JPEG so this is aesthetic only)
    // We output JPEG which doesn't support transparency, so circle clipping is purely visual in the UI

    outCanvas.toBlob(
      (blob) => {
        if (!blob) {
          errorMsg.value = 'Gagal memproses gambar. Coba lagi.';
          processing.value = false;
          return;
        }
        const file = new File([blob], 'cropped.jpg', { type: 'image/jpeg' });
        emit('crop', file);
        processing.value = false;
        close();
      },
      'image/jpeg',
      props.outputQuality
    );
  } catch (err) {
    errorMsg.value = 'Error: ' + (err.message || 'Gagal crop gambar');
    processing.value = false;
  }
}

// ── Close / cleanup ───────────────────────────────────────────────────────────
function close() {
  dragging = false;
  img = null;
  ctx = null;
  emit('update:show', false);
}

onUnmounted(() => {
  img = null;
  ctx = null;
});
</script>

<style>
/* ═══════════════════════════════════════════════════════════════
   NEO CROPPER — Modal Chrome
   ═══════════════════════════════════════════════════════════════ */
.neo-crop-overlay {
  position: fixed;
  inset: 0;
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0, 0, 0, 0.92);
  backdrop-filter: blur(4px);
  padding: 1rem;
}

.neo-crop-card {
  background: #09090b;
  border: 3px solid #3f3f46;
  box-shadow: 8px 8px 0 0 #FFDE00;
  width: 100%;
  max-width: 400px;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.neo-crop-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem 1.25rem;
  border-bottom: 2px solid #3f3f46;
}

/* ═══════════════════════════════════════════════════════════════
   CANVAS CONTAINER
   ═══════════════════════════════════════════════════════════════ */
.neo-crop-canvas-wrap {
  width: min(340px, calc(100vw - 60px));
  height: min(340px, calc(100vw - 60px));
  margin: 0 auto;
  background: #000;
  position: relative;
  overflow: hidden;
}

.neo-crop-canvas-wrap canvas {
  display: block;
  width: 100%;
  height: 100%;
  cursor: grab;
}

/* ═══════════════════════════════════════════════════════════════
   CONTROLS
   ═══════════════════════════════════════════════════════════════ */
.neo-crop-controls {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 1.25rem;
  border-top: 2px solid #3f3f46;
}

.neo-crop-slider {
  flex: 1;
  -webkit-appearance: none;
  appearance: none;
  height: 4px;
  background: #3f3f46;
  border-radius: 2px;
  outline: none;
  cursor: pointer;
}
.neo-crop-slider::-webkit-slider-thumb {
  -webkit-appearance: none;
  width: 18px; height: 18px;
  border-radius: 50%;
  background: #FFDE00;
  border: 2px solid #000;
  cursor: pointer;
}
.neo-crop-slider::-moz-range-thumb {
  width: 18px; height: 18px;
  border-radius: 50%;
  background: #FFDE00;
  border: 2px solid #000;
  cursor: pointer;
}

/* ═══════════════════════════════════════════════════════════════
   ACTION BUTTONS
   ═══════════════════════════════════════════════════════════════ */
.neo-crop-actions {
  display: flex;
  gap: 0.75rem;
  padding: 1rem 1.25rem;
  border-top: 2px solid #3f3f46;
}

.neo-crop-btn-cancel {
  flex: 1;
  display: flex; align-items: center; justify-content: center;
  gap: 0.4rem;
  padding: 0.6rem 1rem;
  background: transparent;
  border: 2px solid #3f3f46;
  color: #a1a1aa;
  font-family: 'Space Grotesk', sans-serif;
  font-weight: 700;
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  cursor: pointer;
  transition: border-color 0.15s, color 0.15s;
}
.neo-crop-btn-cancel:hover { border-color: #ef4444; color: #ef4444; }

.neo-crop-btn-confirm {
  flex: 1;
  display: flex; align-items: center; justify-content: center;
  gap: 0.4rem;
  padding: 0.6rem 1rem;
  background: #0048FF;
  border: 2px solid #000;
  box-shadow: 3px 3px 0 0 #FFDE00;
  color: #fff;
  font-family: 'Space Grotesk', sans-serif;
  font-weight: 800;
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  cursor: pointer;
  transition: transform 0.1s, box-shadow 0.1s;
}
.neo-crop-btn-confirm:hover:not(:disabled) {
  transform: translate(2px, 2px);
  box-shadow: 1px 1px 0 0 #FFDE00;
}
.neo-crop-btn-confirm:disabled { opacity: 0.6; cursor: not-allowed; }

/* ═══════════════════════════════════════════════════════════════
   ANIMATIONS
   ═══════════════════════════════════════════════════════════════ */
@keyframes neo-spin-anim { to { transform: rotate(360deg); } }
.neo-spin { animation: neo-spin-anim 0.8s linear infinite; }

.crop-fade-enter-active { transition: opacity 0.2s ease; }
.crop-fade-leave-active { transition: opacity 0.15s ease; }
.crop-fade-enter-from,
.crop-fade-leave-to { opacity: 0; }
</style>
