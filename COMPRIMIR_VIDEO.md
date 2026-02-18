# 🎬 Solución: Comprimir Video para InfinityFree

## Problema

El archivo `login_video.mp4` (16.7 MB) es demasiado grande para InfinityFree y no se carga correctamente.

## ✅ Solución: Comprimir el Video

Necesitamos reducir el tamaño del video a ~2-3 MB manteniendo la calidad visual.

---

## Opción 1: Usar Herramienta Online (Más Fácil)

### CloudConvert (Recomendado)

1. Ve a: https://cloudconvert.com/mp4-converter
2. Click en **"Select File"**
3. Sube `d:\ASIR\2º\IAW\my_web\assets\login_video.mp4`
4. Configura las opciones:
   - **Format**: MP4
   - Click en el ícono de configuración (⚙️)
   - **Video Codec**: H.264
   - **Resolution**: 1280x720 (o 854x480 para archivo más pequeño)
   - **Video Bitrate**: 500 kbps
   - **Audio Codec**: AAC
   - **Audio Bitrate**: 128 kbps
5. Click en **"Convert"**
6. Descarga el video comprimido
7. Reemplaza el archivo original en `assets/login_video.mp4`
8. Sube el nuevo archivo a InfinityFree

### FreeConvert (Alternativa)

1. Ve a: https://www.freeconvert.com/video-compressor
2. Sube `login_video.mp4`
3. Configura:
   - **Target Size**: 3 MB
   - **Resolution**: 720p
4. Click en **"Compress Now"**
5. Descarga y reemplaza

---

## Opción 2: Usar FFmpeg (Si lo tienes instalado)

Si tienes FFmpeg instalado en Windows:

```bash
cd d:\ASIR\2º\IAW\my_web\assets

# Crear versión comprimida (720p, ~2-3 MB)
ffmpeg -i login_video.mp4 -vf scale=1280:720 -c:v libx264 -crf 28 -preset medium -c:a aac -b:a 128k login_video_compressed.mp4

# Reemplazar el original
move login_video.mp4 login_video_original.mp4
move login_video_compressed.mp4 login_video.mp4
```

---

## Opción 3: Usar HandBrake (Software Gratuito)

1. Descarga HandBrake: https://handbrake.fr/downloads.php
2. Instala y abre HandBrake
3. Arrastra `login_video.mp4` a HandBrake
4. Configura:
   - **Preset**: Fast 720p30
   - **Video Codec**: H.264
   - **Quality**: RF 28
   - **Audio**: AAC, 128 kbps
5. Click en **"Start Encode"**
6. Guarda como `login_video_compressed.mp4`
7. Reemplaza el original

---

## Opción 4: Usar VLC Media Player

Si tienes VLC instalado:

1. Abre VLC
2. Media → Convert/Save
3. Add → Selecciona `login_video.mp4`
4. Convert/Save
5. Profile: Video - H.264 + MP3 (MP4)
6. Settings:
   - Video Codec: H.264
   - Bitrate: 500 kb/s
   - Resolution: 1280x720
7. Start
8. Guarda como `login_video_compressed.mp4`

---

## 📊 Tamaños Esperados

| Configuración | Tamaño Aproximado | Calidad |
|---------------|-------------------|---------|
| 1920x1080, 1000 kbps | ~5-6 MB | Excelente |
| 1280x720, 500 kbps | ~2-3 MB | Muy buena ✅ |
| 854x480, 300 kbps | ~1-2 MB | Buena |

**Recomendación**: 1280x720 a 500 kbps (2-3 MB)

---

## 🚀 Después de Comprimir

1. Verifica que el nuevo archivo sea < 5 MB
2. Prueba el video localmente en tu navegador
3. Sube el video comprimido a InfinityFree:
   - Usa **FileZilla (FTP)** para archivos grandes
   - Reemplaza `htdocs/assets/login_video.mp4`
4. Limpia la caché del navegador (Ctrl + Shift + R)
5. Recarga la página de login

---

## ⚡ Solución Más Rápida

**Usa CloudConvert** (Opción 1):
- No requiere instalar nada
- Proceso automático
- Resultado en 2-3 minutos
- Tamaño optimizado automáticamente

---

¿Quieres que te guíe con alguna de estas opciones específicamente?
