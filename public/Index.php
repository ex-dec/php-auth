﻿<!DOCTYPE html>
<html lang="en">

<head>
    <title>
        Enggano Integration Gateway Platform
    </title>
</head>

<body id="kt_body" class="app-blank bgi-size-cover bgi-attachment-fixed bgi-position-center bgi-no-repeat">
    <script>
        var defaultThemeMode = "light";
        var themeMode;
        if (document.documentElement) {
            if (document.documentElement.hasAttribute("data-bs-theme-mode")) {
                themeMode = document.documentElement.getAttribute("data-bs-theme-mode");
            } else {
                if (localStorage.getItem("data-bs-theme") !== null) {
                    themeMode = localStorage.getItem("data-bs-theme");
                } else {
                    themeMode = defaultThemeMode;
                }
            }
            if (themeMode === "system") {
                themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
            }
            document.documentElement.setAttribute("data-bs-theme", themeMode);
        }
    </script>
    <!--end::Theme mode setup on page load-->
    <!--begin::Root-->
    <div class="d-flex flex-column flex-root" id="kt_app_root">
        <!--begin::Page bg image-->
        <style>
            body {
                background-image: url('media/auth/bg4.jpg');
            }

            [data-bs-theme="dark"] body {
                background-image: url('media/auth/bg4-dark.jpg');
            }
        </style>
        <!--end::Page bg image-->
        <!--begin::Authentication - Sign-in -->
        <div class="d-flex flex-column flex-column-fluid flex-lg-row align-items-center">
            <!--begin::Aside-->
            <div class="d-flex flex-center w-lg-50 pt-15 pt-lg-0 px-10">
                <!--begin::Aside-->
                <div class="d-flex flex-center flex-lg-start flex-column">
                    <!--begin::Logo-->
                    <a href="index.html" class="mb-7">
                        <img alt="Logo" width="150" height="150" class="rounded-4"
                            src="https://media.licdn.com/dms/image/v2/D560BAQEiQj6Z70m2DA/company-logo_200_200/company-logo_200_200/0/1706246808132/enggano_logo?e=2147483647&v=beta&t=NuU51tFrIiHC5ywRFaGCdY1dKdwVUVaml54Oylew-4k" />
                    </a>
                    <!--end::Logo-->
                    <!--begin::Title-->
                    <h2 class="text-white fs-1 fw-normal m-0">Enggano Integration Gateway Platform</h2>
                    <!--end::Title-->
                </div>
                <!--begin::Aside-->
            </div>
            <!--begin::Aside-->
            <!--begin::Body-->
            <div
                class="d-flex flex-column-fluid flex-lg-row-auto justify-content-center justify-content-lg-end p-12 p-lg-20">
                <!--begin::Card-->
                <div
                    class="bg-body d-flex flex-column align-items-stretch flex-center rounded-4 w-md-600px h-md-400px px-20 py-10">
                    <!--begin::Wrapper-->
                    <div class="d-flex flex-center flex-column flex-column-fluid px-lg-10 pb-15 pb-lg-20">
                        <!--begin::Form-->
                        <form class="form w-100" novalidate="novalidate" id="kt_sign_in_form" action="/auth/login" method="GET">
                            <!--begin::Heading-->
                            <div class="text-center mb-11">
                                <!--begin::Title-->
                                <h1 class="text-gray-900 fw-bolder mb-3">Akses Dokumentasi Teknis Integrasi</h1>
                                <div class="text-gray-500 fw-semibold fs-6">Silakan login untuk mengakses dokumentasi teknis integrasi <br>Enggano Platform</div>
                                <div class="text-gray-500 fw-semibold fs-6"></div>
                            </div>
                            <div class="d-grid mb-10">
                                <a href="/auth/login" class="btn btn-flex btn-outline btn-text-gray-700 btn-active-color-primary bg-state-light flex-center text-nowrap w-100">
                                    <img alt="Logo" src="~/media/logos/bgn-icon.svg" class="h-15px me-3">
                                    Login dengan SSO Badan Gizi Nasional
                                </a>
                            </div>
                            <!--end::Submit button-->
                        </form>
                        <!--end::Form-->
                    </div>
                    <!--end::Wrapper-->
                    <!--begin::Footer-->
                    <p class="fw-semibold text-center text-secondary-emphasis fs-base">&copy; 2024 Badan Gizi Nasional</p>
                    <!--end::Footer-->
                </div>
                <!--end::Card-->
            </div>
            <!--end::Body-->
        </div>
        <!--end::Authentication - Sign-in-->
    </div>
</body>
<!--end::Body-->

</html>