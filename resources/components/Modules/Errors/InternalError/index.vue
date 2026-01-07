<template>
  <v-card
    class="internal-error-card border-radius d-block bg-white border-0 shadow-none text-center"
  >
    <div class="internal-error-content">
      <v-img src="/images/internal-error.png" alt="internal-error-image" />
      <h4>Looks like we have an internal error, please try again later.</h4>
      <p>
        But no worries! Our team is looking ever where while you wait safely.
      </p>
      <RouterLink class="btn" to="/admin"> Back to Dashboard </RouterLink>
    </div>
  </v-card>
</template>

<script setup>
defineOptions({
  name: "InternalError",
});
</script>

<style lang="scss" scoped>
.internal-error-card {
  padding-bottom: 100px !important;

  .internal-error-content {
    .v-responsive {
      justify-content: center;
    }
    h4 {
      margin: {
        top: 30px;
        bottom: 12px;
      }
    }
    .btn {
      height: auto;
      min-width: auto;
      margin-top: 12px;
      border-radius: 7px;
      padding: 14px 25px;
      display: inline-block;
      color: var(--whiteColor);
      background-color: var(--primaryColor);
      font: {
        size: 16px;
        weight: 500;
      }
    }
  }
}

/* Max width 767px */
@media only screen and (max-width: 767px) {
  .internal-error-card {
    padding-bottom: 40px;

    .internal-error-content {
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
}

/* Min width 576px to Max width 767px */
@media only screen and (min-width: 576px) and (max-width: 767px) {
}

/* Min width 768px to Max width 991px */
@media only screen and (min-width: 768px) and (max-width: 991px) {
  .internal-error-card {
    padding-bottom: 60px;

    .internal-error-content {
      .btn {
        font-size: 14px;
        margin-top: 7px;
      }
    }
  }
}

/* Min width 992px to Max width 1199px */
@media only screen and (min-width: 992px) and (max-width: 1199px) {
  .internal-error-card {
    padding-bottom: 80px;

    .internal-error-content {
      .btn {
        font-size: 15px;
      }
    }
  }
}
</style>
