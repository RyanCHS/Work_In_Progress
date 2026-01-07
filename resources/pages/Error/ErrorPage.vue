<template>
  <div class="not-found-area">
    <div class="d-table">
      <div class="d-table-cell">
        <div class="not-found-content text-center ml-auto mr-auto">
          <v-img src="/images/error.png" alt="error-image" />
          <h4>Looks like we did not find this page, please try again later.</h4>
          <p>
            But no worries! Our team is looking ever where while you wait
            safely.
          </p>
          <RouterLink class="btn" to="/admin"> Back to Home </RouterLink>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineOptions({
  name: "ErrorPage",
});
</script>

<style lang="scss" scoped>
.not-found-area {
  background-color: var(--whiteColor);
  overflow-x: hidden;
  height: 100vh;
  padding: {
    top: 30px;
    bottom: 30px;
  }
  &.dark-theme {
    background-color: #0a0e19;
  }
}
.not-found-content {
  max-width: 960px;

  .v-responsive {
    justify-content: center;
  }
  h4 {
    margin: {
      top: 30px;
      bottom: 13px;
    }
  }
  .btn {
    height: auto;
    min-width: auto;
    margin-top: 10px;
    border-radius: 7px;
    padding: 14px 25px;
    display: inline-block;
    color: var(--whiteColor) !important;
    background-color: var(--primaryColor);
    font: {
      size: 16px;
      weight: 500;
    }
  }
}

/* Max width 767px */
@media only screen and (max-width: 767px) {
  .not-found-content {
    max-width: 100%;

    h4 {
      margin-top: 25px;
    }
    .btn {
      padding: 12px 25px;
      font-size: 13px;
      margin-top: 2px;
    }
  }
}

/* Min width 576px to Max width 767px */
@media only screen and (min-width: 576px) and (max-width: 767px) {
  .not-found-content {
    max-width: 540px;
  }
}

/* Min width 768px to Max width 991px */
@media only screen and (min-width: 768px) and (max-width: 991px) {
  .not-found-content {
    max-width: 720px;

    .btn {
      font-size: 14px;
      margin-top: 7px;
    }
  }
}

/* Min width 992px to Max width 1199px */
@media only screen and (min-width: 992px) and (max-width: 1199px) {
  .not-found-content {
    .btn {
      font-size: 15px;
    }
  }
}
</style>
