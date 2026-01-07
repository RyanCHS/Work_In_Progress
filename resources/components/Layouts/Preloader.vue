<template>
  <div class="preloader" id="preloader">
    <div class="preloader">
      <div class="waviy position-relative">
        <span class="d-inline-block">L</span>
        <span class="d-inline-block">O</span>
        <span class="d-inline-block">A</span>
        <span class="d-inline-block">D</span>
        <span class="d-inline-block">I</span>
        <span class="d-inline-block">N</span>
        <span class="d-inline-block">G</span>
        <span class="d-inline-block">.</span>
        <span class="d-inline-block">.</span>
        <span class="d-inline-block">.</span>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  // eslint-disable-next-line vue/multi-word-component-names
  name: "Preloader",
};
</script>

<style lang="scss" scoped>
#preloader {
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 9999;
  position: fixed;
  background-color: #fff;
}
#preloader .preloader {
  transform: translate(-50%, -50%);
  position: absolute;
  left: 50%;
  top: 50%;
}
#preloader .preloader .loader {
  position: relative;
  overflow: hidden;
  display: block;
  height: 150px;
  width: 150px;
  margin-left: auto;
  margin-right: auto;
}
#preloader .preloader .loader div {
  height: 100%;
}
#preloader .preloader .loader,
#preloader .preloader .loader div {
  padding: 8px;
  border-radius: 50%;
  border: 2px solid transparent;
  animation: rotate linear 3.5s infinite;
  border-top-color: #605dff;
  border-border-bottom: #605dff;
}
#preloader .preloader .waviy {
  position: relative;
  text-align: center;
  -webkit-box-reflect: below -47px linear-gradient(transparent, rgba(0, 0, 0, 0.2));
  font-size: 50px;
  font-weight: 700;
}
#preloader .preloader .waviy span {
  position: relative;
  color: #000000;
  animation-delay: 0.1s;
  display: inline-block;
  animation: waviy 1s infinite;
}
#preloader .preloader .waviy span:nth-child(2) {
  animation-delay: 0.2s;
}
#preloader .preloader .waviy span:nth-child(3) {
  animation-delay: 0.3s;
}
#preloader .preloader .waviy span:nth-child(4) {
  animation-delay: 0.4s;
}
#preloader .preloader .waviy span:nth-child(5) {
  animation-delay: 0.5s;
}
#preloader .preloader .waviy span:nth-child(6) {
  animation-delay: 0.1s;
}
#preloader .preloader .waviy span:nth-child(7) {
  animation-delay: 0.2s;
}
#preloader .preloader .waviy span:nth-child(8) {
  animation-delay: 0.3s;
}
#preloader .preloader .waviy span:nth-child(9) {
  animation-delay: 0.4s;
}
#preloader .preloader .waviy span:nth-child(10) {
  animation-delay: 0.5s;
}

@keyframes rotate {
  0% {
    transform: rotate(0deg);
  }
  50% {
    transform: rotate(180deg);
  }
  100% {
    transform: rotate(360deg);
  }
}
@keyframes waviy {
  0%,
  40%,
  100% {
    transform: translateY(0);
  }
  20% {
    transform: translateY(-20px);
  }
}
/* Max width 767px */
@media only screen and (max-width: 767px) {
  #preloader .preloader .waviy {
    font-size: 30px;
  }
}
</style>
