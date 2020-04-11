import Vue from 'vue'
import App from './App.vue'

import './assets/main.css'
import config from '../../config.json'
console.log({ config })

Vue.config.productionTip = false

new Vue({
  render: h => h(App),
  provide: { config },
}).$mount('#app')
