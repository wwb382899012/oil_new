<template>
  <div class="hello">
    <h1>{{ msg }}</h1>
  </div>
</template>

<script>
import {post} from '@/utils/request'
export default {
  name: 'HelloWorld',
  data () {
    return {
      msg: ''
    }
  },
  beforeRouteEnter (to, from, next) {
    next(async vm => {
      let ret = await post('/site/setRole', {id: to.query.id})
      console.log(ret)
    })
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
h1, h2 {
  font-weight: normal;
}
ul {
  list-style-type: none;
  padding: 0;
}
li {
  display: inline-block;
  margin: 0 10px;
}
a {
  color: #42b983;
}
</style>
