const fs = require('fs')
const path = require('path')
const canvas = require('canvas')
const faceapi = require('@vladmandic/face-api')

// Patch canvas untuk face-api
const { Canvas, Image, ImageData } = canvas
faceapi.env.monkeyPatch({ Canvas, Image, ImageData })

const MODELS_PATH = path.join(__dirname, '..', '..', 'public', 'models', 'face-api')

let modelsLoaded = false
async function ensureModelsLoaded() {
  if (modelsLoaded) return
  await faceapi.nets.ssdMobilenetv1.loadFromDisk(MODELS_PATH)
  await faceapi.nets.faceLandmark68Net.loadFromDisk(MODELS_PATH)
  await faceapi.nets.faceRecognitionNet.loadFromDisk(MODELS_PATH)
  modelsLoaded = true
}

async function extractDescriptor(imagePath, outputPath) {
  try {
    if (!fs.existsSync(imagePath)) {
      console.error('Image file not found: ' + imagePath)
      process.exit(1)
    }

    await ensureModelsLoaded()

    const image = await canvas.loadImage(imagePath)
    const detection = await faceapi
      .detectSingleFace(image)
      .withFaceLandmarks()
      .withFaceDescriptor()

    if (!detection || !detection.descriptor) {
      console.error('No face detected in image')
      process.exit(1)
    }

    const descriptor = Array.from(detection.descriptor)
    fs.writeFileSync(outputPath, JSON.stringify(descriptor))
    process.exit(0)
  } catch (error) {
    console.error('Error:', error.message)
    process.exit(1)
  }
}

const imagePath = process.argv[2]
const outputPath = process.argv[3]

if (!imagePath || !outputPath) {
  console.error('Usage: node extract-descriptor.cjs <image-path> <output-path>')
  process.exit(1)
}

extractDescriptor(imagePath, outputPath)
