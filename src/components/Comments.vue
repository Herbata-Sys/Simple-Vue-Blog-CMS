<template>
  <div :style="this[theme]" class="comments">
    <div class="comments__title">Komentarze ({{ getCommentsLength }})</div>

    <div class="comments__content">
      <div v-if="!comments.length" class="comments__noComments">Brak komentarzy</div>

      <div @click="login_Expand" v-if="!$store.state.user.logged" class="comments__notLogged">
        Aby dodawać komentarze musisz się <u>zalogować</u>
      </div>
      <div id="comment" v-if="$store.state.user.logged && !viewOnly" class="comments__addComment">
        <textarea class="comments__addText" placeholder="Twój komentarz" v-model="text"></textarea>
        <button class="comments__addButton" @click="addComment(text)">Napisz</button>
      </div>

      <div class="comments__comment" :id="'comment' + comment.id" v-for="comment in comments" :key="comment.id">

        <div class="comments__commentInfo">
          <div class="comments__commentAuthor">
            <img class="comments__authorAvatar" :src='comment.user_avatar == "" ? "/img/user.svg" : comment.user_avatar'>
            <router-link :to="'/article/' + comment.article + '#comment' + comment.id" class="comments__anchor">#</router-link>
            {{ comment.author }}
            <div class="comments__admin" v-if="$store.state.user.admin==='1' && !viewOnly">
              <router-link class="comments__deleteUser" :to="'/admin/deleteUser/'+comment.author_id" title="Usuń użytkownika">[Usuń użytkownika]</router-link>
            </div>

            <div class="comments__admin" v-if="$store.state.user.admin==='1' && !viewOnly">
              <router-link class="comments__deleteComment" :to="'/admin/deleteComment/'+comment.id" title="Usuń komentarz">[Usuń komentarz]</router-link>
            </div>
          </div>

          <div class="comments__commentDate">
            {{ comment.date }}
          </div>
        </div>

        <div class="comments__commentContent">
          <pre>{{ comment.content }}</pre>
        </div>

        <div class="comments__commentButtons">
          <img title="Odpowiedz" class="comments__commentReplyButton" src="/img/comment.svg" @click="toggleAnswer(comment)">

          <font-awesome-icon @click="commentVote(comment, 1)" title="Zgadzam się" class="comments__commentUpvote" :class="votedCheck(comment, 'up')" icon="plus-square"/>
          <div class="comments__commentUpvotes">
            {{ comment.up }}
          </div>

          <font-awesome-icon @click="commentVote(comment, -1)" title="Nie zgadzam się" class="comments__commentDownvote" :class="votedCheck(comment, 'down')" icon="minus-square"/>
          <div class="comments__commentDownvotes">
            {{ comment.down }}
          </div>
        </div>

        <div v-if="$store.state.user.logged" class="comments__addComment comments--hideReply comments--reply" :key="componentKey" :class="[comment.toggleAnswer ? 'comments--showReply' : '']">
          <textarea class="comments__addText" placeholder="Twój komentarz" v-model="comment.replyText"></textarea>
          <button class="comments__addButton" @click="reply(comment.replyText, comment)">Wyślij</button>
        </div>

        <div :key="componentKey2" v-if="comment.answers.length" class="comments__answersTitle">
            Odpowiedzi ({{ comment.answers.length }})

            <div class="comments__answersToggle" @click="toggleAnswers(comment)">
              {{ !comment.showAnswers ? 'Pokaż' : 'Schowaj' }}
            </div>
        </div>

        <div v-if="comment.answers.length && comment.showAnswers" class="comments__commentAnswers">

          <div class="comments__commentAnswer" v-for="answer in comment.answers" :key="answer.id">
            <div class="comments__answerInfo">
              <div class="comments__answerAuthor">
                <img class="comments__authorAvatar" :src='answer.user_avatar == "" ? "/img/user.svg" : answer.user_avatar'>
                {{ answer.author }}
                <div class="comments__admin" v-if="$store.state.user.admin==='1' && !viewOnly">
                  <router-link class="comments__deleteUser" :to="'/admin/deleteUser/'+answer.author_id" title="Usuń użytkownika">[Usuń użytkownika]</router-link>
                </div>

                <div class="comments__admin" v-if="$store.state.user.admin==='1' && !viewOnly">
                  <router-link class="comments__deleteComment" :to="'/admin/deleteAnswer/'+answer.id" title="Usuń komentarz">[Usuń komentarz]</router-link>
                </div>
              </div>

              <div class="comments__answerDate">
                {{ answer.date }}
              </div>
            </div>

            <div class="comments__answerContent">
              <pre>{{ answer.content }}</pre>
            </div>

            <div class="comments__answerButtons">
              <img title="Odpowiedz" class="comments__answerReplyButton" src="/img/comment.svg" @click="toggleAnswer(answer)">

              <font-awesome-icon @click="answerVote(answer, 1)" title="Zgadzam się" class="comments__answerUpvote" :class="votedCheck(answer, 'up')" icon="plus-square"/>
              <div class="comments__answerUpvotes">
                {{ answer.up }}
              </div>

              <font-awesome-icon @click="answerVote(answer, -1)" title="Nie zgadzam się" class="comments__answerDownvote" :class="votedCheck(answer, 'down')" icon="minus-square"/>
              <div class="comments__answerDownvotes">
                {{ answer.down }}
              </div>
            </div>

            <div v-if="$store.state.user.logged" class="comments__addComment comments--hideReply comments--reply" :key="componentKey" :class="[answer.toggleAnswer ? 'comments--showReply' : '']">
              <textarea class="comments__addText" placeholder="Twój komentarz" v-model="answer.replyText"></textarea>
              <button class="comments__addButton" @click="reply(answer.replyText, comment, answer)">Wyślij</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { mapMutations, mapActions } from 'vuex';

export default {
  name: 'Comments',

  props: {
    viewOnly: Boolean,
    comments: Array,
  },

  data() {
    return {
      userAvatar: '',
      componentKey: 0,
      componentKey2: 500,
      text: '',
      delay: false,
      lightCss: {
        '--answer-background': '#f9f9f9',
        '--button-hover': '#fdd872',
        '--border-comment': '#d2d2d2',
        '--not-logged-background': '#e0e0e0',
        '--comments-title-background': '#fdd872',
      },
      darkCss: {
        '--answer-background': '#131313',
        '--button-hover': 'black',
        '--border-comment': 'black',
        '--not-logged-background': '#2d2d2d',
        '--comments-title-background': '#ffb10030',
      },
    };
  },

  computed: {
    theme() {
      return this.$store.state.theme;
    },

    getCommentsLength() {
      let sum = this.comments.length;
      this.comments.forEach((comment) => {
        sum += comment.answers.length;
      });
      return sum;
    },
  },

  methods: {
    ...mapMutations([
      'login_Expand',
    ]),

    ...mapActions([
      'showInfo',
    ]),

    commentVote(comment, value) {
      if (this.$store.state.user.logged) {
        if (comment.voted === undefined) {
          const formdata = new FormData();
          formdata.append('type', value === 1 ? 'up' : 'down');
          formdata.append('comment_id', comment.id);
          const config = { headers: { 'Content-Type': 'application/x-www-form-urlencoded' } };

          axios
            .post('/api/voteComment.php', formdata, config)
            .then((response) => {
              response = response.data;
              if (response.success === true) {
                if (value === 1) comment.up = parseInt(comment.up, 10) + 1;
                else comment.down = parseInt(comment.down, 10) + 1;

                comment.voted = value;
              }
              this.showInfo(response.info);
            });
        } else if (comment.voted === 1 && value === 1) {
          const formdata = new FormData();
          formdata.append('type', value === 1 ? 'up' : 'down');
          formdata.append('comment_id', comment.id);
          const config = { headers: { 'Content-Type': 'application/x-www-form-urlencoded' } };

          axios
            .post('/api/voteComment.php', formdata, config)
            .then((response) => {
              response = response.data;
              if (response.success === true) {
                comment.up = parseInt(comment.up, 10) - 1;
                comment.voted = undefined;
              }
              this.showInfo(response.info);
            });
        } else if (comment.voted === -1 && value === -1) {
          const formdata = new FormData();
          formdata.append('type', value === 1 ? 'up' : 'down');
          formdata.append('comment_id', comment.id);
          const config = { headers: { 'Content-Type': 'application/x-www-form-urlencoded' } };

          axios
            .post('/api/voteComment.php', formdata, config)
            .then((response) => {
              response = response.data;
              if (response.success === true) {
                comment.down = parseInt(comment.down, 10) - 1;
                comment.voted = undefined;
              }
              this.showInfo(response.info);
            });
        }
      } else this.showInfo('Musisz się zalogować, aby dodawać oceny do komentarzy');
    },

    answerVote(answer, value) {
      if (this.$store.state.user.logged) {
        if (answer.voted === undefined) {
          const formdata = new FormData();
          formdata.append('type', value === 1 ? 'up' : 'down');
          formdata.append('answer_id', answer.id);
          const config = { headers: { 'Content-Type': 'application/x-www-form-urlencoded' } };

          axios
            .post('/api/voteAnswer.php', formdata, config)
            .then((response) => {
              response = response.data;
              if (response.success === true) {
                if (value === 1) answer.up = parseInt(answer.up, 10) + 1;
                else answer.down = parseInt(answer.down, 10) + 1;

                answer.voted = value;
              }
              this.showInfo(response.info);
            });
        } else if (answer.voted === 1 && value === 1) {
          const formdata = new FormData();
          formdata.append('type', value === 1 ? 'up' : 'down');
          formdata.append('answer_id', answer.id);
          const config = { headers: { 'Content-Type': 'application/x-www-form-urlencoded' } };

          axios
            .post('/api/voteAnswer.php', formdata, config)
            .then((response) => {
              response = response.data;
              if (response.success === true) {
                answer.up = parseInt(answer.up, 10) - 1;
                answer.voted = undefined;
              }
              this.showInfo(response.info);
            });
        } else if (answer.voted === -1 && value === -1) {
          const formdata = new FormData();
          formdata.append('type', value === 1 ? 'up' : 'down');
          formdata.append('answer_id', answer.id);
          const config = { headers: { 'Content-Type': 'application/x-www-form-urlencoded' } };

          axios
            .post('/api/voteAnswer.php', formdata, config)
            .then((response) => {
              response = response.data;
              if (response.success === true) {
                answer.down = parseInt(answer.down, 10) - 1;
                answer.voted = undefined;
              }
              this.showInfo(response.info);
            });
        }
      } else this.showInfo('Musisz się zalogować, aby dodawać oceny do komentarzy');
    },

    votedCheck(comment, type) {
      return [
        // add class to picked vote
        (comment.voted === 1) && type === 'up' ? 'comments--voted' : '',
        (comment.voted === -1) && type === 'down' ? 'comments--voted' : '',
        // add gray color to other option
        (comment.voted === 1) && type === 'down' ? 'comments--votedGray' : '',
        (comment.voted === -1) && type === 'up' ? 'comments--votedGray' : '',
      ];
    },

    toggleAnswer(comment) {
      if (this.$store.state.user.logged) {
        comment.toggleAnswer = !comment.toggleAnswer;
        this.componentKey += 1;
      } else {
        this.showInfo('Musisz być zalogowany');
        this.login_Expand();
      }
    },

    addComment(commentText) {
      if (!this.delay) {
        if (commentText.length >= 5) {
          const comment = {
            author: this.$store.state.user.name,
            date: this.getDate(),
            content: commentText,
            up: 0,
            down: 0,
            answers: [],
            user_avatar: this.userAvatar,
          };

          const formdata = new FormData();
          formdata.append('content', commentText);
          formdata.append('article_id', this.$route.params.id);
          const config = { headers: { 'Content-Type': 'application/x-www-form-urlencoded' } };

          axios
            .post('/api/addComment.php', formdata, config)
            .then((response) => {
              response = response.data;
              if (response.success === true) {
                comment.id = response.data.comment_id;
                this.comments.unshift(comment);
                this.text = '';

                this.delay = true;
                setTimeout(() => { this.delay = false; }, 60000);
              }
              this.showInfo(response.info);
            });
        } else this.showInfo('Twój komentarz musi zawierać co najmniej 5 znaków!');
      } else this.showInfo('Musisz poczekać trochę dłużej aby dodać kolejny komentarz');
    },

    reply(replyText, comment, answer) {
      if (!this.delay) {
        if (replyText) {
          if (replyText.length >= 5) {
            const newReply = {
              author: this.$store.state.user.name,
              date: this.getDate(),
              content: replyText,
              up: 0,
              down: 0,
              user_avatar: this.userAvatar,
            };

            const formdata = new FormData();
            formdata.append('content', replyText);
            formdata.append('comment_id', comment.id);
            formdata.append('article', this.$route.params.id);
            const config = { headers: { 'Content-Type': 'application/x-www-form-urlencoded' } };

            axios
              .post('/api/addReply.php', formdata, config)
              .then((response) => {
                response = response.data;
                if (response.success === true) {
                  newReply.id = response.data.answer_id;
                  comment.answers.unshift(newReply);
                  if (!answer) comment.replyText = '';
                  else answer.replyText = '';

                  comment.showAnswers = true;
                  this.componentKey2 += 1;

                  this.delay = true;
                  setTimeout(() => { this.delay = false; }, 60000);
                }
                this.showInfo(response.info);
              });
          } else this.showInfo('Twój komentarz musi zawierać co najmniej 5 znaków!');
        } else this.showInfo('Twój komentarz nie może być pusty!');
      } else this.showInfo('Musisz poczekać trochę dłużej aby dodać kolejny komentarz');
    },

    getDate() {
      const date = new Date();

      const day = (`0${date.getDate()}`).slice(-2);
      const monthIndex = (`0${date.getMonth() + 1}`).slice(-2);
      const year = date.getFullYear();
      const hour = date.getHours();
      const minutes = date.getMinutes();

      return `${day}-${monthIndex}-${year} ${hour}:${minutes}`;
    },

    toggleAnswers(comment) {
      comment.showAnswers = !comment.showAnswers;
      this.componentKey2 += 1;
    },

    getUserAvatar() {
      axios.get('/api/getUserAvatar.php')
        .then((response) => {
          this.userAvatar = response.data.data.avatar;
        });
    },
  },

  mounted() {
    setTimeout(() => {
      if (location.hash.length > 0) document.querySelector(window.location.hash).scrollIntoView();
    }, 2000);
    this.getUserAvatar();
  },
};
</script>

<style scoped lang="scss">
.comments{
  margin: 40px 0;
  text-align: center;

  &__title{
    display: inline-block;
    text-align: center;
    font-weight: 600;
    background: var(--comments-title-background);
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 40px;
    border: 1px solid #ffbc00;
  }

  &__noComments{
    text-align: center;
    margin: 30px 0;
  }

  &__addComment{
    display: flex;
    flex-wrap: wrap;
    margin-bottom: 50px;
  }

  &__anchor{
    color: var(--main-text-color);
  }

  &__addText{
    width: 100%;
    max-width: 100%;
    min-width: 100%;
    min-height: 50px;
    padding: 10px;
    font-size: 17px;
    font-family: 'Montserrat', 'Avenir', Helvetica, Arial, sans-serif;
    margin-bottom: 10px;
    margin-top: 10px;
    color: var(--main-text-color);
    background: none;
  }

  &__addButton{
    background: none;
    color: var(--text-color);
    border: 1px solid #ffbc00;
    font-weight: 900;
    padding: 5px 10px;
    cursor: pointer;
    transition: background-color .4s, transform .4s;
    will-change: background-color, transform;

    &:hover{
      background-color: var(--button-hover);
      transform: translateY(-3px);
    }
  }

  &__comment, &__commentAnswer{
    text-align: left;
    padding: 10px;
    border: 1px solid var(--border-comment);
    margin-bottom: 30px;
    border-radius: 3px;
    border-bottom: 4px solid #ffbc00;
  }

  &_comment{
    animation: slide-in-blurred-tl 0.3s cubic-bezier(0.230, 1.000, 0.320, 1.000) both;
  }

  &__commentAnswers{
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-end;
    margin-top: 20px;
  }

  &__commentAnswer{
    width: 98%;
    border-bottom: 1px solid black;
    background: var(--answer-background);
  }

  &__answersToggle{
    display: inline-block;
    font-weight: 900;
    cursor: pointer;
    margin-left: 10px;
  }

  &__commentAuthor, &__answerAuthor{
    display: flex;
    align-items: center;
  }

  &__authorAvatar{
    display: inline-block;
    height: 50px;
    width: 50px;
    border: 1px solid #bfbfbf;
    margin-right: 10px;
    border-radius: 100px;
  }

  &__admin{
    display: inline-block;
  }

  &__deleteUser{
    color: red;
    font-weight: 900;
    text-decoration: none;
    padding-left: 5px;
  }

  &__deleteComment{
    color: red;
    font-weight: 900;
    text-decoration: none;
    padding-left: 5px;
  }

  &__answersTitle{
    width: 98%;
    font-weight: 600;
    text-align: left;
    margin: 10px 0 10px 20px;
  }

  &__commentInfo, &__answerInfo{
    font-size: 13px;
    border-bottom: 1px solid var(--border-comment);
    padding: 4px;
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    user-select: none;
  }

  &__commentContent, &__answerContent{
    padding: 20px 10px;
    border-bottom: 2px solid var(--border-comment);
  }

  &__commentButtons, &__answerButtons{
    display: flex;
    user-select: none;
  }

  &__commentUpvote, &__commentDownvote, &__answerUpvote, &__answerDownvote{
    font-size: 18px;
    align-self: center;
    margin: 0 10px;
    cursor: pointer;
    transition: transform .4s;
    will-change: transform;

    &:hover{
      transform: scale(1.2);
    }
  }

  &__commentUpvotes, &__commentDownvotes, &__answerUpvotes, &__answerDownvotes{
    align-self: center;
    font-size: 12px;
    font-weight: 600;
  }

  &__commentUpvote, &__answerUpvote{
    color: #0064ff;
    will-change: transform;
  }

  &__commentDownvote, &__answerDownvote{
    color: #ff6c00;
    will-change: transform;
  }

  &__commentReplyButton, &__answerReplyButton{
    width: 45px;
    height: 45px;
    cursor: pointer;
    transition: transform .4s;
    will-change: transform;

    &:hover{
      transform: translateY(-3px);
    }
  }

  &__notLogged{
    color: var(--text-color);
    padding: 10px;
    margin: 10px 0;
    background-color: var(--not-logged-background);
    cursor: pointer;
  }

  &--voted{
    transform: rotate(25deg);
  }

  &--votedGray{
    color: gray;
  }

  &--hideReply{
    display: none;
  }

  &--showReply{
    display: block;
  }

  &--reply{
    margin-bottom: 10px;
  }
}

@keyframes slide-in-blurred-tl {
  0% {
    transform: translate(-1000px, -1000px) skew(80deg, 10deg);
    transform-origin: 100% 0%;
    opacity: 0;
  }
  100% {
    transform: translate(0, 0) skew(0deg, 0deg);
    transform-origin: 50% 50%;
    opacity: 1;
  }
}
</style>
