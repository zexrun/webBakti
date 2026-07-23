/**
 * Renders the given pixel crop region of `imageSrc` onto an offscreen
 * canvas and resolves a JPEG Blob of just that region. Standard pattern
 * adapted from react-easy-crop's own docs, returning a Blob (for
 * FormData upload) instead of a data URL.
 */
export function getCroppedImg(imageSrc, croppedAreaPixels) {
  return new Promise((resolve, reject) => {
    const image = new Image()
    image.crossOrigin = 'anonymous'
    image.onload = () => {
      const canvas = document.createElement('canvas')
      canvas.width = croppedAreaPixels.width
      canvas.height = croppedAreaPixels.height
      const ctx = canvas.getContext('2d')

      ctx.drawImage(
        image,
        croppedAreaPixels.x,
        croppedAreaPixels.y,
        croppedAreaPixels.width,
        croppedAreaPixels.height,
        0,
        0,
        croppedAreaPixels.width,
        croppedAreaPixels.height,
      )

      canvas.toBlob(
        (blob) => (blob ? resolve(blob) : reject(new Error('Gagal memproses gambar.'))),
        'image/jpeg',
        0.8,
      )
    }
    image.onerror = () => reject(new Error('Gagal memuat gambar.'))
    image.src = imageSrc
  })
}
