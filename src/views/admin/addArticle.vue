<template>
  <div class="addarticle" :style="this[theme]">
    <h2><font-awesome-icon icon="plus-square"/> Dodaj artykuł</h2>

    <form class="password__form" v-on:submit.prevent="addArticle">
      <div class="addarticle__info">Tytuł artykułu (maksymalnie 500 znaków):</div>
      <input v-model="newArticle.title" type="text" class="addarticle__input" placeholder="Tytuł artykułu" maxlength="500" minlength="3" required>

      <div class="addarticle__info">Zdjęcie do artykułu (rozszerzenie <b>.jpg</b> lub <b>.jpeg</b>, minimalna preferowana szerokość <b>1100px</b>):</div>
      <input type="file" accept="image/jpeg,.jpg,.jpeg" ref="file" v-on:change="onChangeFileUpload" required>
      <div class="addarticle__logo">
        <img v-if="url" :src="url" />
      </div>

      <div class="addarticle__info">Krótki tekst, który będzie widoczny na stronie głównej pod artykułem <b>(nie zawiera znaczników ani formatowania)</b>:</div>
      <textarea v-model="newArticle.shortText" class="addarticle__textarea" placeholder="Krótki tekst" maxlength="2000" minlength="10" required></textarea>

      <div class="addarticle__info">Treść całego artykułu <b>(z formatowaniem i znacznikami)</b></div>
      <textarea v-model="newArticle.text" class="addarticle__textarea" placeholder="Treść artykułu" minlength="50" required></textarea>

      <div class="addarticle__info">Tagi (wybierz z istniejących lub utwórz nowy, <b>min. 1 tag</b>):</div>
      <div class="tags">
        <div class="tags__picked">
          <div class="tags__tag tags--active" v-for="tag in pickedTags" :key="tag.key" title="Usuń tag" @click="removeTag(tag.id)">
            {{ tag.name }}
            <font-awesome-icon icon="times"/>
          </div>
        </div>
        <input @keyup.enter.prevent="addTagEnter" ref="tagInput" @keydown.enter.prevent @keypress.space.prevent="addTagEnter" @keydown.delete="removeTagBackspace(searchTag)" type="text" class="tags__input" placeholder="Tagi" maxlength="500" minlength="3" v-model="searchTag">
      </div>
      <div class="tags__show">
        <div class="tags__tag tags--notActive tags--createTag" v-if="searchTag" @click="createTag">
          <font-awesome-icon icon="plus-square"/>
          Utwórz tag "{{ searchTag }}"
        </div>
        <div class="tags__tag tags--notActive" v-for="tag in searchFilter(tags)" :key="tag.key" title="Dodaj tag" @click="addTag(tag.id)">
          {{ tag.name }}
        </div>
      </div>

      <input type="submit" class="addarticle__submit" value="Dodaj">
    </form>
  </div>
</template>

<script>
import { mapActions } from 'vuex';
import axios from 'axios';

export default {
  name: 'addArticle',

  data() {
    return {
      url: null,
      file: '',
      pickedTags: [],
      searchTag: '',
      tags: [],
      newArticle: {},
      lightCss: {
        '--main-text-color': 'black',
      },
      darkCss: {
        '--main-text-color': '#d8d8d8',
      },
    };
  },

  computed: {
    theme() {
      return this.$store.state.theme;
    },
  },

  methods: {
    ...mapActions([
      'showInfo',
    ]),

    getTags() {
      axios.get('/api/getTags.php')
        .then((response) => {
          this.tags = response.data.data;
        });
    },

    searchFilter(value) {
      const searched = [];
      value.forEach((element) => {
        if (element) if (element.name.toLowerCase().search(this.searchTag.toLowerCase()) !== -1) searched.push(element);
      });
      return searched;
    },

    addTag(id) {
      if (this.pickedTags.findIndex(x => x.id === id) < 0) {
        this.pickedTags.push(this.tags.find(x => x.id === id));
        this.searchTag = '';
        this.tags.splice(this.tags.findIndex(x => x.id === id), 1);
      }
      this.$refs.tagInput.focus();
    },

    addTagEnter() {
      if (this.searchFilter(this.tags)[0]) {
        const { id } = this.searchFilter(this.tags)[0];

        if (this.pickedTags.findIndex(x => x.id === id) < 0) {
          this.pickedTags.push(this.tags.find(x => x.id === id));
          this.searchTag = '';
          this.tags.splice(this.tags.findIndex(x => x.id === id), 1);
        }
      }
    },

    removeTag(id) {
      this.tags.push(this.pickedTags[this.pickedTags.findIndex(x => x.id === id)]);
      this.pickedTags.splice(this.pickedTags.findIndex(x => x.id === id), 1);
      this.$refs.tagInput.focus();
    },

    removeTagBackspace(text) {
      if (text.length <= 0 && this.pickedTags.length) {
        this.tags.push(this.pickedTags[this.pickedTags.length - 1]);
        this.pickedTags.pop();
      }
    },

    createTag() {
      const formdata = new FormData();
      formdata.append('name', this.searchTag);
      const config = { headers: { 'Content-Type': 'application/x-www-form-urlencoded' } };

      axios
        .post('/api/createTag.php', formdata, config)
        .then((response) => {
          response = response.data;
          if (response.success) {
            this.tags.push(response.data);
            this.pickedTags.push(response.data);
            this.searchTag = '';
          }
          this.showInfo(response.info);
        });
      this.$refs.tagInput.focus();
    },

    addArticle() {
      const formdata = new FormData();
      formdata.append('title', this.newArticle.title);
      formdata.append('shortText', this.newArticle.shortText);
      formdata.append('text', this.newArticle.text);
      formdata.append('file', this.file);
      const tags = [];
      this.pickedTags.forEach((tag) => {
        tags.push(tag.id);
      });
      formdata.append('tags', JSON.stringify(tags));
      const config = { headers: { 'Content-Type': 'multipart/form-data' } };

      axios
        .post('/api/addArticle.php', formdata, config)
        .then((response) => {
          response = response.data;
          if (response.success) {
            this.newArticle = {};
            this.$refs.file.type = 'text';
            this.$refs.file.type = 'file';
            this.pickedTags = [];
            this.$router.push({ path: `/article/${response.data}` });
          }
          this.showInfo(response.info);
        });
    },

    onChangeFileUpload() {
      [this.file] = this.$refs.file.files;
      this.url = URL.createObjectURL(this.file);
    },
  },

  mounted() {
    this.getTags();
  },
};
</script>

<style lang="scss" scoped>
.addarticle{
  &__form{
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    margin: 30px 0;
  }

  &__input{
    display: block;
    background: transparent;
    padding: 5px;
    font-size: 18px;
    margin: 0 0 10px 0;
    width: 100%;
    border: 1px solid #ffbc00;
    color: var(--main-text-color);
  }

  &__textarea{
    display: block;
    background: transparent;
    padding: 5px;
    font-size: 18px;
    margin: 0 0 10px 0;
    width: 100%;
    min-width: 100%;
    max-width: 100%;
    height: 200px;
    border: 1px solid #ffbc00;
    color: var(--main-text-color);
  }

  &__submit{
    cursor: pointer;
    display: block;
    background: transparent;
    padding: 5px;
    margin: 20px 0;
    border: 1px solid #ffbc00;
    font-weight: 600;
    font-size: 20px;
    color: var(--main-text-color);
  }

  &__info{
    margin-top: 20px;
    padding: 10px;
    font-size: 17px;
    background: #ffbc00;
    color: black;
  }

  &__logo img{
    max-width: 100%;
  }
}


.tags{
  border: 1px solid #ffbc00;
  padding: 5px;
  margin: 0 0 0 0;
  border-bottom: none;

  &__input{
    border: none;
    background: transparent;
    font-size: 18px;
    padding: 6px;
    color: var(--main-text-color);
    width: 150px;
  }

  &__show{
    padding: 5px;
    border: 1px solid #c1c1c1;
    min-height: 100px;
  }

  &__picked{
    display: inline;
    padding-right: 5px;
  }

  &__tag{
    cursor: pointer;
    font-weight: 600;
    display: inline-block;
    padding: 6px;
    margin: 0 3px;
    background: white;
    border-radius: 5px;
  }

  &--active{
    background: #ffa500;
    font-size: 13px;
    margin: 3px 3px;
  }

  &--notActive{
    background: black;
    color: white;
    margin: 8px 5px;
    font-size: 13px;
  }

  &--createTag{
    background: #036075;
  }
}
</style>
