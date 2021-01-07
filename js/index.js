Vue.component('bier-formulier', {
    template: `
    <div>
        <label>Naam: </label><input type="text" v-model="biertje.naam"><br>
        <label>Brouwer: </label><input type="text" v-model="biertje.brouwer"><br>
        <label>Type: </label><input type="text" v-model="biertje.type"><br>
        <label>Gisting: </label><input type="text" v-model="biertje.gisting"><br>
        <label>Alc. Percentage: </label><input type="text" v-model="biertje.perc"><br>
        <label>Inkoop prijs: </label><input type="text" v-model="biertje.inkoop_prijs"><br>
        <input type="submit" value="Opslaan" v-on:click="saveBeer">
    <div>
    `,
    props: ['biertje'],
    methods: {
        //update functie
        saveBeer: function () {
            $.ajax({
                method: "POST",
                url: "api.php?action=updateBeer",
                data: this.biertje
            })
            .then(function (response) {
                response.bSuccess
                document.getElementById("bierForm").style.top = "-400px";
            })
            .catch(function (error) {
                console.log(error);
            })
        },
        closeForm: function () {
            document.getElementById("bierForm").style.top = "-400px";
        }
    }
})

//fields aanmaken voor de api
var app3 = new Vue({
    el: '#app3',
    data() {
        return {
            biertjes: [],
            fields: null,
            selBier: {},
        }
    },
//connectie maken met de bier api
    created() {
        $.getJSON('api.php?action=getBeer')
            .then(response => {
                this.biertjes = response.data;
                this.fields = Object.keys(this.biertjes[0]);
            })
            .catch(error => {
                console.log(error);
            })
    },
    methods: {
        //update tabel tevoorschijn halen
        updateBier: function (bier) {
            this.selBier = bier;
            document.getElementById("bierForm").style.top = "300px";
        },
        //delete functie
        deleteBier: function (bier) {
            this.selBier = bier;
            $.ajax({
                method: "POST",
                url: "api.php?action=deleteBeer",
                data: bier
            })
            .then(function (response) {
                location.reload();
            })
            .catch(function (error) {
                alert(error);
            })
        }
    }
});