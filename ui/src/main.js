import Vue from 'vue'
import App from './App.vue'

import './assets/main.css'

Vue.config.productionTip = false
Vue.prototype.$apiRoot = 'http://localhost:8080/api/v1/'

new Vue({
  render: h => h(App),
}).$mount('#app')
