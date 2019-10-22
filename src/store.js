import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

export default new Vuex.Store({

  state: {
    theme: 'lightCss',
    blog: '',
    page: 1,
    infoMessage: '',
    infoShow: false,
    loginExpanded: false,
    user: {
      id: null,
      email: '',
      name: '',
      reg_date: '',
      admin: false,
      logged: false,
    },
    articles: [
      {
        id: 0,
        title: '',
        image: 'none.jpg',
        shortText: '',
      },
    ],
  },

  mutations: {
    set_Info(state, message) {
      state.infoMessage = message;
    },
    set_Info_Show(state, value) {
      state.infoShow = value;
    },
    set_Page(state, value) {
      state.page = value;
    },
    login_Expand(state) {
      state.loginExpanded = !state.loginExpanded;
    },
    set_User(state, payload) {
      state.user = payload;
      localStorage.setItem('user', JSON.stringify(state.user));
    },
    set_Blog(state, payload) {
      state.blog = payload;
      localStorage.setItem('blog', JSON.stringify(payload));
    },
    set_Articles(state, articles) {
      state.articles = articles;
    },
    push_Articles(state, articles) {
      state.articles = state.articles.concat(articles);
    },
  },

  getters: {

  },

  actions: {
    showInfo(context, message) {
      context.commit('set_Info', message);
      context.commit('set_Info_Show', true);
      setTimeout(() => { context.commit('set_Info_Show', false); }, 6000);
    },
  },

});
