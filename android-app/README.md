# App Android nativa — AI Company

Cascarón nativo (Capacitor) que abre la plataforma web en vivo, más un **widget
de pantalla de inicio** escrito en Kotlin. La parte web se sigue actualizando
sola con cada push; solo el cascarón y el widget necesitan recompilar.

## Compilar
```
cd android-app/android
JAVA_HOME="C:/Program Files/Android/Android Studio/jbr" ANDROID_HOME="C:/Android/Sdk" ./gradlew assembleRelease
```
Sale en `android/app/build/outputs/apk/release/app-release.apk`.

Necesita `android/keystore.properties` (está en `.gitignore`) apuntando a la llave
del vault. Ver "App Android nativa — llave de firma" en el vault.

## Instalar en el celular
Copiar el `.apk` al celular (WhatsApp a uno mismo, Drive, cable) y abrirlo.
Android pide permitir "instalar de origen desconocido" una vez. Cada versión nueva
se instala encima sin perder nada.

## El widget
Mantener presionado en la pantalla de inicio → Widgets → **AI Company** → arrastrar.
Al soltarlo pide la `WIDGET_KEY` (la variable de Railway). Queda guardada en el
celular y solo sirve para leer el resumen.

Se refresca cada 30 minutos (mínimo que permite Android). Tocarlo abre la app.

## Versionar
Subir `versionCode` y `versionName` en `android/app/build.gradle` antes de cada
APK nuevo, o Android rechaza instalar "una versión igual".
