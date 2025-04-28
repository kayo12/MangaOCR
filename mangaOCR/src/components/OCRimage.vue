<template>
    <div>
        <h2>OCR manga</h2>
        <input type="file" ref="cleanInput" @change="onFileChange" accept="image/*" />
    </div>

    <div ref="imagePreviewContainer">
       
    </div>

    <div v-if="loading">Carregando...</div>
    <div v-else-if="textcurrent">
        <pre>{{ textcurrent }}</pre>
    </div>

</template>

<script setup>
import { ref } from 'vue' 
import Tesseract from 'tesseract.js'

const cleanInput = ref(null)
const loading = ref(false)  
const textcurrent = ref('')
const imagePreviewContainer = ref(null)


function convertGrayScale(canvas) {
  // Function to convert an image to grayscale
  try{
    const ctx = canvas.getContext('2d');
    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
    const data = imageData.data;

    for (let i = 0; i < data.length; i += 4) {
      const average = (data[i] + data[i + 1] + data[i + 2]) / 3;
      data[i] = data[i + 1] = data[i + 2] = average; // Set RGB to the average value
    }

    ctx.putImageData(imageData, 0, 0);
    return canvas;
  }catch(err){
    console.log('Erro ao tentar processar convertGrayScale' + err)
}
  
}

function resizeImage(canvas, maxWidth = 1200) {
  try{
  const ctx = canvas.getContext('2d',{ willReadFrequently: true });
  const scale = maxWidth / canvas.width;

  const newWidth = maxWidth;
  const newHeight = canvas.height * scale;

  const tempCanvas = document.createElement('canvas');
  tempCanvas.width = newWidth;
  tempCanvas.height = newHeight;

  const tempCtx = tempCanvas.getContext('2d',{ willReadFrequently: true });
  tempCtx.drawImage(canvas, 0, 0, newWidth, newHeight);
  return tempCanvas;
}catch(e){
  console.log('Erro no processamento do resizerImage' + err)
}
  
}


function applyThreshold(canvas, threshold = 128) {
  
  try{

      const ctx = canvas.getContext('2d',{ willReadFrequently: true });
      const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
      const data = imageData.data;

      for (let i = 0; i < data.length; i += 4) {
        const r = data[i];
        const g = data[i + 1];
        const b = data[i + 2];

        // Calcula a média (luminosidade aproximada)
        const gray = 0.3 * r + 0.59 * g + 0.11 * b;

        // Aplica o limiar (threshold)
        const value = gray >= threshold ? 255 : 0;

        data[i] = data[i + 1] = data[i + 2] = value; // Define como branco ou preto
        // Mantém o alpha (transparência) intacto
      }

  ctx.putImageData(imageData, 0, 0);
  return canvas;
}catch(err){
  console.log('Erro ao tentar processar applThreshold' + err)
}
  
}


function ProcessImage(file){
  const objectURL = URL.createObjectURL(file);
    //realizar préprocessamento da imagem
    return new Promise((resolve, reject) => {
      const  imgProcess = new Image()

      imgProcess.onload = () =>{
        try{
              const canvas = document.createElement('canvas')
              canvas.width = imgProcess.width
              canvas.height = imgProcess.height

              const context = canvas.getContext('2d',{ willReadFrequently: true })
              context.drawImage(imgProcess, 0, 0)
              let precanvas = resizeImage(canvas)
              precanvas = convertGrayScale(precanvas);
              precanvas = applyThreshold(precanvas)

              resolve(precanvas)
            }catch(err){
            console.log('Erro na função ProcessImage' + err)
          }
        }
        imgProcess.onerror = () => {
          URL.revokeObjectURL(objectURL); // Limpe a URL aqui também!
          reject(new Error('Erro ao carregar imagem processImage')); // Rejeita com um objeto Error
        };
      imgProcess.src = objectURL; 

    })
  

    

}

function VisualizationImage(file){


    return new Promise((resolve, reject) => {

      const readFile = new FileReader()

      readFile.onload = () =>{

        const img = new Image();

        img.onload = () =>{
          resolve({
            name: file.name,
            type: file.type,
            size: file.size,
            width: img.width,
            height: img.height
          })

          if(imagePreviewContainer.value){
            if(imagePreviewContainer.value.firstChild){
              console.log('entrou na condição')
              imagePreviewContainer.value.removeChild(imagePreviewContainer.value.firstElementChild)
            }

            imagePreviewContainer.value.appendChild(img);
          }
        }
          img.onerror = () => reject('Reject img.onload: Erro ao carregar imagem')
          img.src = readFile.result;

         readFile.onerror = (err) => {
         console.error('Erro ao ler arquivo com FileReader:', file.name, err);
         reject(`Erro ao ler arquivo: ${file.name}`); // Rejeita a Promise
        };

      }
      readFile.readAsDataURL(file); 
  })
}

async function onFileChange(file) {

  const fileReady = file.target.files[0]

    console.log('Arquivo selecionado:', file)

    if (fileReady !== null && fileReady !== undefined) {
        loading.value = true

  //visualizar imagem do upload
   VisualizationImage(fileReady)
  //pré-processamento da imagem para melhor otimização da extração.
  const fileFinal = await ProcessImage(fileReady)
console.log(fileFinal.toDataURL())
    Tesseract.recognize(
      fileFinal.toDataURL(),
          'eng+por+jpn+jpn_vert',
          {
            logger: m => console.log(m)
          }
        ).then(({ data: { text: extractedText } }) => {
          textcurrent.value = extractedText
          loading.value = false
          console.log(textcurrent)

          cleanInput.value.value = null // Limpa o input após o processamento
        }).catch(err => {
          console.error('Erro no OCR:', err)
          loading.value = false
        })
      }
    }


</script>

<style>

</style>