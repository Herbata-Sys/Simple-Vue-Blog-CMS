import Vue from 'vue';
import Router from 'vue-router';
import Home from './views/Home.vue';
import Article from './views/Article.vue';
import Tags from './views/Tags.vue';
import Tag from './views/Tag.vue';
import Search from './views/Search.vue';

import User from './views/User.vue';
import userHome from './views/user/userHome.vue';
import changePassword from './views/user/changePassword.vue';
import changeAvatar from './views/user/changeAvatar.vue';
import myComments from './views/user/myComments.vue';
import deleteAccount from './views/user/deleteAccount.vue';

import blog from './views/admin/blog.vue';
import addArticle from './views/admin/addArticle.vue';
import articles from './views/admin/articles.vue';
import users from './views/admin/users.vue';
import editArticle from './views/admin/editArticle.vue';
import deleteArticle from './views/admin/deleteArticle.vue';
import deleteUser from './views/admin/deleteUser.vue';
import removeAdmin from './views/admin/removeAdmin.vue';
import addAdmin from './views/admin/addAdmin.vue';
import deleteComment from './views/admin/deleteComment.vue';
import deleteAnswer from './views/admin/deleteAnswer.vue';

Vue.use(Router);

export default new Router({
  mode: 'history',
  base: process.env.BASE_URL,
  routes: [
    {
      path: '/',
      name: 'home',
      component: Home,
    },
    {
      path: '/article/:id',
      name: 'article',
      component: Article,
    },
    {
      path: '/tags/',
      name: 'tags',
      component: Tags,
    },
    {
      path: '/tag/:id',
      name: 'tag',
      component: Tag,
    },
    {
      path: '/search/',
      name: 'search',
      component: Search,
    },
    {
      path: '/user/',
      component: User,
      children: [
        {
          path: '',
          name: 'userHome',
          component: userHome,
        },
        {
          path: 'changePassword',
          name: 'changePassword',
          component: changePassword,
        },
        {
          path: 'changeAvatar',
          name: 'changeAvatar',
          component: changeAvatar,
        },
        {
          path: 'myComments',
          name: 'myComments',
          component: myComments,
        },
        {
          path: 'deleteAccount',
          name: 'deleteAccount',
          component: deleteAccount,
        },
        {
          path: '/admin/blog',
          name: 'blog',
          component: blog,
        },
        {
          path: '/admin/addArticle',
          name: 'addArticle',
          component: addArticle,
        },
        {
          path: '/admin/articles',
          name: 'articles',
          component: articles,
        },
        {
          path: '/admin/users',
          name: 'users',
          component: users,
        },
        {
          path: '/admin/editArticle/:id',
          name: 'editArticle',
          component: editArticle,
        },
        {
          path: '/admin/deleteArticle/:id',
          name: 'deleteArticle',
          component: deleteArticle,
        },
        {
          path: '/admin/deleteUser/:id',
          name: 'deleteUser',
          component: deleteUser,
        },
        {
          path: '/admin/removeAdmin/:id',
          name: 'removeAdmin',
          component: removeAdmin,
        },
        {
          path: '/admin/addAdmin/:id',
          name: 'addAdmin',
          component: addAdmin,
        },
        {
          path: '/admin/deleteComment/:id',
          name: 'deleteComment',
          component: deleteComment,
        },
        {
          path: '/admin/deleteAnswer/:id',
          name: 'deleteAnswer',
          component: deleteAnswer,
        },
      ],
    },
  ],
  scrollBehavior() {
    return { x: 0, y: 0 };
  },
});
