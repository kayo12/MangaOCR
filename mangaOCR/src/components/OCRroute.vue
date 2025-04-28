<template>
    <div>
        <h2>OCR MANGA</h2>
        <input type="file" @change="SendImage" ref="cleanInput" accept="image/*" >
        <img :src="imagePreview" alt="">
    </div>
</template>

<script setup>
    import {ref} from 'vue'

    const cleanInput = ref(null)
    const imagePreview = ref(null)

    async function SendImage(event) {
    
    const file = event.target.files[0];
    imagePreview.value = URL.createObjectURL(file);
     
    const formtData = new FormData();
    formtData.append("image",file);
 
    try {
        const response = await fetch('http://127.0.0.1:8000/api/test', {
            method: 'POST',
            body: formtData
        })
        const data = await response.json();
        console.log("Resposta do servidor:", data)
    }catch(error){

        console.error("Erro ao enviar imagem: ", error)
    }
}
</script>
<style>
</style>