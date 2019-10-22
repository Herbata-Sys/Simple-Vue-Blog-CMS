import Vue from 'vue';

import axios from 'axios';
import _ from 'lodash.debounce';
import VueAxios from 'vue-axios';

import { library } from '@fortawesome/fontawesome-svg-core';
import {
  faSearch,
  faStream,
  faThLarge,
  faWindowClose,
  faAdjust,
  faArrowRight,
  faPlusSquare,
  faMinusSquare,
  faSignOutAlt,
  faBars,
  faUser,
  faUserTie,
  faKey,
  faCommentAlt,
  faAngleDown,
  faAngleUp,
  faColumns,
  faNewspaper,
  faUsers,
  faTimes,
  faChevronRight,
  faEdit,
} from '@fortawesome/free-solid-svg-icons';

import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

import App from './App.vue';
import router from './router';
import store from './store';

Vue.use(VueAxios, axios, _);


library.add(faSearch, faStream, faThLarge, faWindowClose, faAdjust, faArrowRight, faPlusSquare, faMinusSquare, faSignOutAlt, faBars, faUser, faUserTie, faKey, faCommentAlt, faAngleDown, faAngleUp, faColumns, faNewspaper, faUsers, faTimes, faChevronRight, faEdit);

Vue.component('font-awesome-icon', FontAwesomeIcon);

Vue.config.productionTip = false;

new Vue({
  router,
  store,
  render: h => h(App),
}).$mount('#app');
