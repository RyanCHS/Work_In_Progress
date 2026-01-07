<template>
  <div class="sign-in-area">
    <div class="trezo-form ml-auto mr-auto">
      <div class="row align-items-center">
        <div class="col-lg-6 col-md-12 order-2 order-lg-1">
          <div class="form-image">
            <v-img src="/images/sign-in.jpg" alt="sign-in-image" />
          </div>
        </div>
        <div class="col-lg-6 col-md-12 order-1 order-lg-2">
          <div class="form-content">
            <div class="title">
              <h1 class="fw-semibold">Silahkan Login!</h1>
            </div>
            <form @submit.prevent="handleLogin">
              <div class="trezo-form-group">
                <v-label class="d-block fw-medium text-black">
                  Username
                </v-label>
                <v-text-field
                  v-model="username"
                  label="Username"
                ></v-text-field>
              </div>
              <div class="trezo-form-group">
                <v-label class="d-block fw-medium text-black">
                  Password
                </v-label>
                <v-text-field
                  type="password"
                  v-model="password"
                  label="Type password"
                ></v-text-field>
              </div>
              <!-- <div class="forgot-password">
                <RouterLink
                  to="/authentication/forgot-password"
                  class="text-primary fw-semibold"
                >
                  Forgot Password?
                </RouterLink>
              </div> -->
              <div v-if="error" class="text-red">{{ error }}</div>
              <button type="submit" class="trezo-btn mb-3" :disabled="loading">
                <i class="material-symbols-outlined">login</i>
                {{ loading ? "Loading..." : "Sign In" }}
              </button>
              <div class="trezo-form-group">
                <p>
                  Don’t have an account.
                  <a
                    href="/authentication/register"
                    class="fw-medium text-primary text-decoration-none"
                    >Register</a
                  >
                </p>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useAuth } from "@composables/useAuth";
import { ref } from "vue";

defineOptions({
  name: "SignIn",
});

const username = ref("");
const password = ref("");
const loading = ref(false);
const error = ref(null);
const { login } = useAuth();

const handleLogin = async () => {
  loading.value = true;
  error.value = null;

  try {
    await login(username.value, password.value);
  } catch (err) {
    // Menampilkan pesan error umum
    error.value = "Username atau Password salah.";
    console.error(err);
  } finally {
    loading.value = false;
  }
};
</script>

<style lang="scss" scoped>
.sign-in-area {
  background-color: var(--whiteColor);
  padding: {
    top: 135px;
    bottom: 135px;
  }
  &.dark-theme {
    background-color: #0a0e19;
  }
  .trezo-form {
    max-width: 1255px;
    padding: {
      left: 12.5px;
      right: 12.5px;
    }
    .form-image {
      border-radius: 24px;
      margin-right: -25px;

      .v-img {
        border-radius: 24px;
        margin-bottom: -3px;
      }
    }
    .form-content {
      padding-left: 90px;

      .title {
        margin: {
          top: 23px;
          bottom: 23px;
        }
        h1 {
          font-size: 28px;
          margin-bottom: 7px;
        }
        p {
          font-size: 16px;
          color: #445164;
        }
      }
      form {
        .trezo-form-group {
          margin-bottom: 15px;

          .v-label {
            margin-bottom: 12px;
          }
        }
        .forgot-password {
          a {
            &:hover {
              text-decoration: underline;
            }
          }
        }
        button {
          width: 100%;
          height: auto;
          min-width: auto;
          margin-top: 24px;
          padding: 12px 25px;
          border-radius: 7px;
          color: var(--whiteColor);
          background-color: var(--primaryColor);
          display: flex;
          align-items: center;
          justify-content: center;

          & {
            font: {
              size: 16px;
              weight: 500;
            }
          }
          i {
            margin-right: 5px;
          }
        }
        .info {
          margin-top: 20px;

          a {
            &:hover {
              text-decoration: underline;
            }
          }
        }
      }
    }
  }
}

/* Max width 767px */
@media only screen and (max-width: 767px) {
  .sign-in-area {
    padding: {
      top: 60px;
      bottom: 60px;
    }
    .trezo-form {
      max-width: 100%;

      .form-image {
        border-radius: 15px;
        margin: {
          right: 0;
          top: 25px;
        }
        img {
          border-radius: 15px;
        }
      }
      .form-content {
        padding-left: 0;

        .title {
          margin: {
            top: 17px;
            bottom: 15px;
          }
          h1 {
            font-size: 22px;
          }
          p {
            font-size: 13px;
          }
        }
        .with-socials {
          margin-bottom: 20px;

          div {
            margin: {
              left: 5px;
              right: 5px;
            }
          }
          button {
            padding: 8px 15px;
          }
        }
        form {
          .trezo-form-group {
            .v-label {
              margin-bottom: 10px;
            }
          }
          button {
            padding: 12px 25px;
            margin-top: 17px;
            font-size: 13px;
            i {
              font-size: 20px;
            }
          }
          .info {
            margin-top: 15px;
          }
        }
      }
    }
  }
}

/* Min width 576px to Max width 767px */
@media only screen and (min-width: 576px) and (max-width: 767px) {
  .sign-in-area {
    .trezo-form {
      max-width: 540px;

      .form-content {
        .title {
          margin: {
            top: 20px;
            bottom: 17px;
          }
        }
        form {
          button {
            margin-top: 20px;
          }
        }
      }
    }
  }
}

/* Min width 768px to Max width 991px */
@media only screen and (min-width: 768px) and (max-width: 991px) {
  .sign-in-area {
    padding: {
      top: 80px;
      bottom: 80px;
    }
    .trezo-form {
      max-width: 720px;

      .form-image {
        margin: {
          right: 0;
          top: 25px;
        }
      }
      .form-content {
        padding-left: 0;

        .title {
          margin: {
            top: 20px;
            bottom: 20px;
          }
          h1 {
            font-size: 24px;
          }
          p {
            font-size: 14px;
          }
        }
        .with-socials {
          margin-bottom: 20px;

          div {
            margin: {
              left: 5px;
              right: 5px;
            }
          }
        }
        form {
          button {
            font-size: 14px;
            margin-top: 20px;
            padding: 12px 25px;
            i {
              font-size: 22px;
            }
          }
          .info {
            margin-top: 15px;
          }
        }
      }
    }
  }
}

/* Min width 992px to Max width 1199px */
@media only screen and (min-width: 992px) and (max-width: 1199px) {
  .sign-in-area {
    padding: {
      top: 100px;
      bottom: 100px;
    }
    .trezo-form {
      max-width: 960px;

      .form-image {
        margin-right: 0;
      }
      .form-content {
        padding-left: 15px;

        .title {
          h1 {
            font-size: 26px;
          }
          p {
            font-size: 15px;
          }
        }
        form {
          button {
            margin-top: 20px;
            font-size: 15px;
          }
          .info {
            margin-top: 15px;
          }
        }
      }
    }
  }
}

/* Min width 1200px to Max width 1399px */
@media only screen and (min-width: 1200px) and (max-width: 1399px) {
  .sign-in-area {
    padding: {
      top: 120px;
      bottom: 120px;
    }
    .trezo-form {
      max-width: 1140px;

      .form-image {
        margin-right: 0;
      }
      .form-content {
        padding-left: 25px;
      }
    }
  }
}

/* Min width 1600px */
@media only screen and (min-width: 1600px) {
  .sign-in-area {
    .trezo-form {
      .form-image {
        margin-right: -45px;
      }
      .form-content {
        padding-left: 120px;
      }
    }
  }
}
</style>
