import Vue from 'vue'
import App from './App.vue'
import VueResource from 'vue-resource'

import './assets/main.css'
import config from '../../config.json'

Vue.config.productionTip = false
Vue.use(VueResource)
Vue.http.options.root = 'http://localhost:8080/api/v1/'
Vue.http.options.headers = { hello: 'world' }
Vue.prototype.$apiRoot = 'http://localhost:8080/api/v1/'

new Vue({
  render: h => h(App),
  provide: { config },
}).$mount('#app')
