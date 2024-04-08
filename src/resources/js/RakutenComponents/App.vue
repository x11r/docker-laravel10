<script>

import axios from 'axios'

export default {
    data: function () {
        return {
            rakutenAreas: []
        }
    },
    mounted() {
        console.log('mount')
    },
    created() {
        this.getAreas()
    },
    methods: {
        getAreas () {
            const url = '/api/rakuten/areas'
            axios
                .get(url)
                .then(response => {
                    this.rakutenAreas = response.data.areas
                })
                .catch(error => {})
        },
        getHotels (param1, param2, param3 = '') {

            let url = '/api/rakuten/hotels?'
            url = url + 'middleClassCode=' + param1
            url = url + '?smallClassCode=' + param2

            axios
                .get(url)
                .then(response => {
                    thsi.rakutenHotels = response.data
                })
                .catch(error => {
                })
        }
    }
}
</script>

<template>
    <div class="container">
        <div class="h2">Rakuten API by Vuejs</div>
<!--        <div>largeClassName : {{ rakutenAreas.areaClasses.largeClasses[0].largeClass[0].largeClassName }}</div>-->
        <div v-for="(middleClass, index) in rakutenAreas.areaClasses.largeClasses[0].largeClass[1].middleClasses">
            <div>{{ middleClass.middleClass[0].middleClassName }}</div>
            <ul>
            <li v-for="(smallClass, index2) in middleClass.middleClass[1].smallClasses">
                <button @click="getHotels(
                    middleClass.middleClass[0].middleClassCode,
                    smallClass.smallClass[0].smallClassCode)">
<!--                    {{ smallClass.smallClass[0].smallClassCode }}-->
                    {{ smallClass.smallClass[0].smallClassName }}
                </button>
                <div v-if="smallClass.smallClass[1]">
                    <ul>
                        <li v-for="(detailClass, index3) in smallClass.smallClass[1].detailClasses">
                            <button @click="getHotels">
                                {{ detailClass.detailClass.detailClassName }}
                            </button>
                        </li>
                    </ul>
                </div>
            </li>
            </ul>
        </div>
    </div>
</template>

<style scoped>

</style>
