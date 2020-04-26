import Vue from 'vue'
import App from './App.vue'

import './assets/main.css'

Vue.config.productionTip = false
Vue.prototype.$apiRoot = process.env.VUE_APP_API_ROOT + '/api/v1/'

new Vue({
  render: h => h(App),
}).$mount('#app')
