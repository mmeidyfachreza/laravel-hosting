<template>
<div>
<div class="visible-print text-center">
<showqr v-if="lokasi" :value="qr" :options="{width: 200}"></showqr>
<p v-else>Silahkan Aktifkan GPS pada perangkat terlebih dahulu</p>
</div>

</div>



</template>

<script>
import showqr from '@chenfengyuan/vue-qrcode';
import axios from 'axios';

export default {
    props:['qrcode','link'],
    data: function() {
        return {
            qr: this.qrcode,
            count: 0,
            show:true,
            location:{
                coords:{
                    latitude:'',
                    longitude:''
                }
            },
            gettingLocation: false,
            errorStr:null,
            lokasi:false,
        }
    },
    components:{showqr},
    created(){
        this.fetchData()
    },
    methods:{
        fetchData(){
            var v = this;
            this.gettingLocation = true;
            // get position
            navigator.geolocation.getCurrentPosition(pos => {
            this.gettingLocation = false;
            this.location = pos;
            this.lokasi = true;
            }, err => {
            this.gettingLocation = false;
            this.errorStr = err.message;
            })
            setTimeout(function () {
                axios
                .post('/api/qrcode/'+v.link+'/',{latitude:v.location.coords.latitude, longitude:v.location.coords.longitude})
                .then(response => {
                    v.qr = response.data.qrcode;
                    this.lokasi = false;
                });
                v.fetchData();
            },2000);
        }
    }
}
</script>

<style>

</style>
