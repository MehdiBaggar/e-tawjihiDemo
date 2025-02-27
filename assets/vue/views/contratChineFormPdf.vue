<template>
  <div class="form-container">
    <h1>Contrat de Service d’Inscriptions</h1>
    <form @submit.prevent="submitForm">
      <div class="form-section">
        <label for="contract-date">le présent contrat de prestaion de service d'inscription a été conclu le </label>
        <input type="date" id="contract-date" v-model="formData.contractDate">
      </div>


      <div class="form-section">
        <h2>Informations sur le Client</h2>
        <label for="tutor-name">tuteur :</label>
        <input type="text" id="tutor-name" v-model="formData.tutor.name" >

        <label for="student-name">Élève :</label>
        <input type="text" id="student-name" v-model="formData.student.name" >

        <label for="student-cin">CIN de l’élève :</label>
        <input type="text" id="student-cin" v-model="formData.student.cin" >

        <label for="tutor-cin">CIN du tuteur :</label>
        <input type="text" id="tutor-cin" v-model="formData.tutor.cin" >

        <label for="receipt-number">Numéro du reçu :</label>
        <input type="text" id="receipt-number" v-model="formData.recuNumber" >
      </div>

      <div class="form-section">
        <h2>Pack et Prix</h2>

        <label>Pack :</label>
        <div>
          <label>
            <input type="radio" v-model="formData.pack.name" value="TAWJIH+" @change="updatePrice">
            TAWJIH+
          </label>

          <label>
            <input type="radio" v-model="formData.pack.name" value="TASSJIL TOP15" @change="updatePrice">
            TASSJIL TOP 15
          </label>

          <label>
            <input type="radio" v-model="formData.pack.name" value="TASSJIL SCIENCE+" @change="updatePrice">
            TASSJIL SCIENCE+
          </label>

          <label>
            <input type="radio" v-model="formData.pack.name" value="TASSJIL ECO+" @change="updatePrice">
            TASSJIL ECO+
          </label>
        </div>

        <label for="pack-price">Prix du service :</label>
        <input type="text" id="pack-price" v-model="formData.pack.price" readonly>
      </div>


      <button type="submit">Générer le PDF</button>
    </form>
  </div>
</template>

<script>
import { PDFDocument, rgb,StandardFonts } from "pdf-lib"; // Import pdf-lib

export default {
  data() {
    return {
      formData: {
        contractDate: '',
        tutor: {
          name: '',
          cin: ''
        },
        student: {
          name: '',
          cin: ''
        },
        recuNumber: '',
        pack:{
          name: '',
          price: '',
        }
      }
    };
  },
  methods:
      {
        updatePrice() {
          const prices = {
            "TAWJIH+": "190 DHS",
            "TASSJIL TOP15": "1800 DHS",
            "TASSJIL SCIENCE+": "2300 DHS",
            "TASSJIL ECO+": "1800 DHS"
          };
          this.formData.pack.price = prices[this.formData.pack.name] || "";
        },
    async submitForm() {
      try {
        const existingPdfBytes = await fetch('/contrat.pdf').then(res => res.arrayBuffer());
        const pdfDoc = await PDFDocument.load(existingPdfBytes);
        const pages = pdfDoc.getPages();
        const helveticaBoldFont = await pdfDoc.embedFont(StandardFonts.HelveticaBold);


        const page1 = pages[0];
        const positionsPage1 = {
          contractDate: { x: 350, y: 739.5 },
          tutorName: { x: 415, y: 701.8 },
          studentName: { x: 415, y: 685 },
          studentCIN: { x: 415, y: 670.5 },
          tutorCIN: { x: 415, y: 654 },
          recuNumber: { x: 415, y: 637 }
        };



        page1.drawText(`${this.formData.contractDate}`, { ...positionsPage1.contractDate, size: 10,font: helveticaBoldFont, color: rgb(0, 0, 0) });
        page1.drawText(`${this.formData.tutor.name}`, { ...positionsPage1.tutorName, size: 10,font: helveticaBoldFont, color: rgb(0, 0, 0) });
        page1.drawText(`${this.formData.student.name}`, { ...positionsPage1.studentName, size: 10,font: helveticaBoldFont, color: rgb(0, 0, 0) });
        page1.drawText(`${this.formData.student.cin}`, { ...positionsPage1.studentCIN, size: 10,font: helveticaBoldFont, color: rgb(0, 0, 0) });
        page1.drawText(`${this.formData.tutor.cin}`, { ...positionsPage1.tutorCIN, size: 10,font: helveticaBoldFont, color: rgb(0, 0, 0) });
        page1.drawText(`${this.formData.recuNumber}`, { ...positionsPage1.recuNumber, size: 10,font: helveticaBoldFont, color: rgb(0, 0, 0) });


        if (pages.length > 1) {
          const page2 = pages[1];

          const positionsPage2 = {
            tutor: { x: 390, y: 120 },
            student: { x: 390, y: 101.5 },
            pack: { x: 390, y: 82.5 },
            price: { x: 390, y: 63 },
          };

          page2.drawText(`${this.formData.tutor.name}`, { ...positionsPage2.tutor, size: 10,font: helveticaBoldFont, color: rgb(0, 0, 0) });
          page2.drawText(`${this.formData.student.name}`, { ...positionsPage2.student, size: 10,font: helveticaBoldFont, color: rgb(0, 0, 0) });
          page2.drawText(`${this.formData.pack.name}`, { ...positionsPage2.pack, size: 10,font: helveticaBoldFont, color: rgb(0, 0, 0) });
          page2.drawText(`${this.formData.pack.price}`, { ...positionsPage2.price, size: 10,font: helveticaBoldFont, color: rgb(0, 0, 0) });
        }


        const pdfBytes = await pdfDoc.save();
        const link = document.createElement('a');
        link.href = URL.createObjectURL(new Blob([pdfBytes], { type: 'application/pdf' }));
        link.download = 'contrat_rempli.pdf';
        link.click();

      } catch (error) {
        console.error('Erreur lors de la génération du PDF', error);
      }
    }
  }

};

</script>

<style>
.form-container {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
}

.form-section {
  margin-bottom: 20px;
}

label {
  display: block;
  margin-bottom: 5px;
}

input[type="text"],
input[type="date"] {
  width: 100%;
  padding: 8px;
  margin-bottom: 10px;
}

button {
  background: #007bff;
  color: white;
  padding: 10px 20px;
  border: none;
  cursor: pointer;
}
</style>
