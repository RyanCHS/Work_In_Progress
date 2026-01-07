<template>
  <div class="footer-area text-center mt-25 bg-white border-top-radius">
    <p>
      ©2025
      <span>N2Indomedia Theme</span>
    </p>
  </div>
</template>

<script setup>
defineOptions({
  name: "MainFooter",
});
</script>

<style lang="scss" scoped>
.footer-area {
  padding: 19.5px 25px;

  span {
    color: #ad63f6;
  }
  a {
    color: var(--primaryColor);
  }
}

@media only screen and (max-width: 767px) {
  .footer-area {
    padding: 15px;
  }
}

@media only screen and (min-width: 768px) and (max-width: 991px) {
  .footer-area {
    padding: 20px;
  }
}
</style>
