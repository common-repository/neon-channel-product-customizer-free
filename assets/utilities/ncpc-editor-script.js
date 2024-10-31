var zoom = 100;
function hexToRgb(hex) {
  // Supprimez le caractère "#" s'il est présent
  hex = hex.replace(/^#/, "");

  // Divisez la valeur hexadécimale en composants R, G et B
  const r = parseInt(hex.substring(0, 2), 16);
  const g = parseInt(hex.substring(2, 4), 16);
  const b = parseInt(hex.substring(4, 6), 16);

  // Retourne une chaîne de caractères au format "rgb(r, g, b)"
  return `${r}, ${g}, ${b}`;
}
function darkenColor(color, factor) {
  // Valider la couleur au format "#RRGGBB"
  if (!/^#[0-9A-Fa-f]{6}$/.test(color)) {
    throw new Error("La couleur doit être au format '#RRGGBB'");
  }

  // Extraire les composants R, G et B
  const r = parseInt(color.slice(1, 3), 16);
  const g = parseInt(color.slice(3, 5), 16);
  const b = parseInt(color.slice(5, 7), 16);

  // Ajuster les composants RGB en fonction du facteur (0.0 à 1.0)
  const newR = Math.max(0, Math.min(255, Math.round(r * factor)));
  const newG = Math.max(0, Math.min(255, Math.round(g * factor)));
  const newB = Math.max(0, Math.min(255, Math.round(b * factor)));

  // Générer la nouvelle couleur au format "#RRGGBB"
  const newColor = `#${newR.toString(16).padStart(2, "0")}${newG
    .toString(16)
    .padStart(2, "0")}${newB.toString(16).padStart(2, "0")}`;

  return newColor;
}

function hideScene() {
  document.documentElement.style.setProperty(
    "--ncpc-scene-visibility",
    "hidden"
  );
  // console.log('hidden')
}
function showScene() {
  document.documentElement.style.setProperty(
    "--ncpc-scene-visibility",
    "visible"
  );
  // console.log('show')
}

// Channel
function changeFaceColor(color) {
  // Accédez à la valeur actuelle de la variable CSS
  const defaultFaceColor = getComputedStyle(
    document.documentElement
  ).getPropertyValue("--ncpc-face");
  // Modifiez la valeur de la variable CSS
  document.documentElement.style.setProperty("--ncpc-face", color);
}

var currentZoom = parseFloat(
  getComputedStyle(document.documentElement).getPropertyValue(
    "--ncpc-font-size"
  )
);
var ok = false;
var ko = false;
var previousLine = 0;

function resize(
  containerWidth,
  containWidth,
  lines,
  containerHeight,
  containHeight
) {
  var currentCutSize = parseFloat(
    getComputedStyle(document.documentElement).getPropertyValue(
      "--ncpc-cutToShape-stroke"
    )
  );
  if (containHeight > containerHeight) {
    // console.log("limite atteinte")
  } else {
    // console.log("reste avant atteinte")
  }

  if (containerWidth < containWidth || containHeight > containerHeight) {
    if (window.currentZoom > 30) {
      currentCutSize -= 1;
      window.currentZoom -= 10;
      document.documentElement.style.setProperty(
        "--ncpc-font-size",
        window.currentZoom
      );
      document.documentElement.style.setProperty(
        "--ncpc-cutToShape-stroke",
        currentCutSize
      );
      ok = true;
    }
  } else if ((containerWidth + 210) / 1.5 > containWidth && ok === true) {
    if (window.currentZoom < 100) {
      window.currentZoom += 10;
      currentCutSize += 1;
      document.documentElement.style.setProperty(
        "--ncpc-font-size",
        window.currentZoom
      );
      document.documentElement.style.setProperty(
        "--ncpc-cutToShape-stroke",
        currentCutSize
      );
    }
  }

  // if (lines > previousLine) {
  //     if (window.currentZoom > 30) {
  //         ko = false;
  //         window.currentZoom -= 10;
  //         document.documentElement.style.setProperty('--ncpc-font-size', window.currentZoom);
  //         //console.log("zoom:", currentZoom);
  //     }
  // } else if (lines < previousLine) {
  //     if (window.currentZoom < 100){
  //         ko = true;
  //         window.currentZoom += 10;
  //         document.documentElement.style.setProperty('--ncpc-font-size', window.currentZoom);
  //         //console.log("zoom:", currentZoom);
  //     }
  // }

  // Mettre à jour previousLine avec la nouvelle valeur de lines
  previousLine = lines;
}

// var previousLine = 0;

// function resize(containerWidth, containWidth, lines) {

//   if (containerWidth < containWidth) {
//     if (window.currentZoom > 30) {
//       window.currentZoom -= 10;
//       updateFontSize(window.currentZoom);
//     }
//   }

//   else if (containerWidth > containWidth && window.currentZoom < 100) {
//     window.currentZoom += 10;
//     updateFontSize(window.currentZoom);
//   }

//   if (lines > previousLine) {
//     if (window.currentZoom > 30) {
//       window.currentZoom -= 10;
//       updateFontSize(window.currentZoom);
//     }
//   }

//   else if (lines < previousLine && window.currentZoom < 100) {
//     window.currentZoom += 10;
//     updateFontSize(window.currentZoom);
//   }

//   previousLine = lines;

// }

// function updateFontSize(zoom) {
//   document.documentElement.style.setProperty('--ncpc-font-size', zoom);
// }

function firstResize() {
  // console.log("firstResize")
  // document.documentElement.style.setProperty('--ncpc-font-size', 60);
  window.addEventListener("DOMContentLoaded", (event) => {
    let root = document.documentElement;
    root.style.setProperty("--ncpc-font-size", 50);
    window.currentZoom = parseFloat(
      getComputedStyle(document.documentElement).getPropertyValue(
        "--ncpc-font-size"
      )
    );
  });
}

function handleAlignText(index) {
  switch (index) {
    case 0:
      document.documentElement.style.setProperty("--ncpc-text-align", "start");
      break;
    case 1:
      document.documentElement.style.setProperty("--ncpc-text-align", "center");
      break;
    case 2:
      document.documentElement.style.setProperty("--ncpc-text-align", "end");
      break;
  }
}
function handleChangeFontFamily(fontFamily, lineHeight) {
  document.documentElement.style.setProperty("--ncpc-font-family", fontFamily);
  document.documentElement.style.setProperty("--ncpc-line-height", "normal");
}

function handleApplyFaceColor(color) {
  document.documentElement.style.setProperty("--ncpc-face", color);
  document.documentElement.style.setProperty("--ncpc-face-dark", color);
}
function handleApplyMetalFaceColor(color) {
  var darkColor = darkenColor(color, 0.8);
  document.documentElement.style.setProperty("--ncpc-face", color);
  document.documentElement.style.setProperty("--ncpc-face-dark", darkColor);
}
function handleApplyTrimColor(color) {
  document.documentElement.style.setProperty("--ncpc-trim", color);
}
function handleApplySideColor(color) {
  document.documentElement.style.setProperty("--ncpc-side", color);
}
function handleApplyLightColor(color) {
  document.documentElement.style.setProperty("--ncpc-light", color);
  document.documentElement.style.setProperty("--ncpc-pre-light", "white");
}

function handleShowBox() {
  document.documentElement.style.setProperty(
    "--ncpc-def-board-color",
    "215, 215, 215"
  );
  document.documentElement.style.setProperty(
    "--ncpc-defBacking-shadow-color",
    "#0000009f"
  );

  document.documentElement.style.setProperty(
    "--ncpc-def-race-color",
    "#00000000"
  );
  document.documentElement.style.setProperty("--ncpc-one-visibility", "hidden");

  document.documentElement.style.setProperty("--ncpc-visibility", "hidden");
}
function handleShowRaceway() {
  document.documentElement.style.setProperty(
    "--ncpc-def-race-color",
    "#D7D7D769"
  );
  document.documentElement.style.setProperty(
    "--ncpc-one-visibility",
    "visible"
  );

  document.documentElement.style.setProperty("--ncpc-visibility", "hidden");

  document.documentElement.style.setProperty(
    "--ncpc-def-board-color",
    "#00000000"
  );
  document.documentElement.style.setProperty(
    "--ncpc-defBacking-shadow-color",
    "#00000000"
  );
}

function handleSetBoxColor(color) {
  var rgbColor = hexToRgb(color);
  // const defBoardColor = getComputedStyle(document.documentElement).getPropertyValue('--ncpc-def-board-color-rgba1');
  // const defBoardColor2 = getComputedStyle(document.documentElement).getPropertyValue('--ncpc-def-board-color-rgba2');
  // console.log(defBoardColor, defBoardColor2, "qsdqsqddqsd")
  document.documentElement.style.setProperty(
    "--ncpc-def-board-color",
    rgbColor
  );
  document.documentElement.style.setProperty(
    "--ncpc-def-race-color",
    "#00000000"
  );
  document.documentElement.style.setProperty("--ncpc-one-visibility", "hidden");

  document.documentElement.style.setProperty("--ncpc-visibility", "hidden");
}
function handleSetRacewayColor(color) {
  document.documentElement.style.setProperty(
    "--ncpc-def-race-color",
    color + "90"
  );
  document.documentElement.style.setProperty(
    "--ncpc-one-visibility",
    "visible"
  );

  document.documentElement.style.setProperty("--ncpc-visibility", "hidden");

  document.documentElement.style.setProperty(
    "--ncpc-def-board-color",
    "#00000000"
  );
}

//fuounctions pour afficher les différents backboards
function handleShowCutToShape() {
  //activation du cut-to-shape
  document.documentElement.style.setProperty(
    "--ncpc-cutToShape-color",
    "#B8B6B694"
  );
  //decactivation du box et du board
  document.documentElement.style.setProperty(
    "--ncpc-def-board-color",
    "#00000000"
  );
  document.documentElement.style.setProperty(
    "--ncpc-defBacking-shadow-color",
    "#00000000"
  );

  document.documentElement.style.setProperty(
    "--ncpc-def-box-color",
    "#00000000"
  );
  document.documentElement.style.setProperty("--ncpc-box-visibility", "hidden");
  //decactivation du raceway
  document.documentElement.style.setProperty(
    "--ncpc-def-Neon-race-color",
    "#00000000"
  );
  document.documentElement.style.setProperty(
    "--ncpc-defStandBase-color",
    "#00000000"
  );
  document.documentElement.style.setProperty("--ncpc-visibility", "hidden");
  //decactivation du stand
  document.documentElement.style.setProperty(
    "--ncpc-stand-visibility",
    "hidden"
  );
}
function handleShowNeonBoard() {
  //activation du board
  document.documentElement.style.setProperty(
    "--ncpc-def-board-color",
    "215, 215, 215"
  );
  document.documentElement.style.setProperty(
    "--ncpc-defBacking-shadow-color",
    "#0000009f"
  );
  //decactivation du raceway
  document.documentElement.style.setProperty("--ncpc-visibility", "hidden");
  //decactivation du cut to shape
  document.documentElement.style.setProperty(
    "--ncpc-cutToShape-color",
    "#00000000"
  );
  //decactivation du box
  document.documentElement.style.setProperty(
    "--ncpc-def-box-color",
    "#00000000"
  );
  document.documentElement.style.setProperty("--ncpc-box-visibility", "hidden");
  //decactivation du stand
  document.documentElement.style.setProperty(
    "--ncpc-defStandBase-color",
    "#00000000"
  );
  document.documentElement.style.setProperty(
    "--ncpc-stand-visibility",
    "hidden"
  );
}
function handleShowNeonBox() {
  //activation du box
  document.documentElement.style.setProperty(
    "--ncpc-def-box-color",
    "215, 215, 215"
  );
  document.documentElement.style.setProperty(
    "--ncpc-box-visibility",
    "visible"
  );
  document.documentElement.style.setProperty(
    "--ncpc-def-board-color",
    "215, 215, 215"
  );
  document.documentElement.style.setProperty(
    "--ncpc-defBacking-shadow-color",
    "#0000009f"
  );

  //decactivation du raceway
  document.documentElement.style.setProperty("--ncpc-visibility", "hidden");
  document.documentElement.style.setProperty("--ncpc-one-visibility", "hidden");
  //decactivation du cut-to-shape
  document.documentElement.style.setProperty(
    "--ncpc-cutToShape-color",
    "#00000000"
  );
  //decactivation stand
  document.documentElement.style.setProperty(
    "--ncpc-defStandBase-color",
    "#00000000"
  );
  document.documentElement.style.setProperty(
    "--ncpc-stand-visibility",
    "hidden"
  );
}
function handleShowNeonRaceway() {
  //activation du raceway
  document.documentElement.style.setProperty(
    "--ncpc-def-Neon-race-color",
    "#D7D7D769"
  );
  document.documentElement.style.setProperty("--ncpc-visibility", "visible");

  document.documentElement.style.setProperty("--ncpc-one-visibility", "hidden");
  //desactivation du board et du box
  document.documentElement.style.setProperty(
    "--ncpc-def-board-color",
    "#00000000"
  );
  document.documentElement.style.setProperty(
    "--ncpc-defBacking-shadow-color",
    "#00000000"
  );

  document.documentElement.style.setProperty(
    "--ncpc-def-box-color",
    "#00000000"
  );
  document.documentElement.style.setProperty("--ncpc-box-visibility", "hidden");
  //desactivation du cut-to-shape
  document.documentElement.style.setProperty(
    "--ncpc-cutToShape-color",
    "#00000000"
  );
  //desactivation du stand
  document.documentElement.style.setProperty(
    "--ncpc-defStandBase-color",
    "#00000000"
  );
  document.documentElement.style.setProperty(
    "--ncpc-stand-visibility",
    "hidden"
  );
}
function handleShowNeonStand() {
  //activation du stand
  document.documentElement.style.setProperty(
    "--ncpc-stand-visibility",
    "visible"
  );
  document.documentElement.style.setProperty(
    "--ncpc-defStandBase-color",
    "#D7D7D769"
  );
  document.documentElement.style.setProperty(
    "--ncpc-def-board-color",
    "215, 215, 215"
  );
  document.documentElement.style.setProperty(
    "--ncpc-defBacking-shadow-color",
    "#0000009f"
  );

  //desactivation du box
  document.documentElement.style.setProperty(
    "--ncpc-def-box-color",
    "#00000000"
  );
  document.documentElement.style.setProperty("--ncpc-box-visibility", "hidden");
  //desactivation du raceway
  document.documentElement.style.setProperty("--ncpc-visibility", "hidden");
  document.documentElement.style.setProperty("--ncpc-one-visibility", "hidden");
  //desactivation du cut-to-shape
  document.documentElement.style.setProperty(
    "--ncpc-cutToShape-color",
    "#00000000"
  );
}
//fonction pour faire apparaitre aucun backboard
function handleNoBack() {
  //désactivation du stand
  document.documentElement.style.setProperty(
    "--ncpc-stand-visibility",
    "invisible"
  );
  document.documentElement.style.setProperty(
    "--ncpc-def-board-color",
    "#00000000"
  );
  document.documentElement.style.setProperty(
    "--ncpc-defBacking-shadow-color",
    "#00000000"
  );

  //desactivation du box
  document.documentElement.style.setProperty(
    "--ncpc-def-box-color",
    "#00000000"
  );
  document.documentElement.style.setProperty("--ncpc-box-visibility", "hidden");
  //desactivation du raceway
  document.documentElement.style.setProperty("--ncpc-visibility", "hidden");
  document.documentElement.style.setProperty("--ncpc-one-visibility", "hidden");
  //desactivation du cut-to-shape
  document.documentElement.style.setProperty(
    "--ncpc-cutToShape-color",
    "#00000000"
  );
}

//fonction pour changer la couleur des backboards
function handleSetNeonCutToShapeColor(color) {
  //activation du cut-to-shape
  document.documentElement.style.setProperty(
    "--ncpc-cutToShape-color",
    color + "90"
  );
  //decactivation du box et du board
  document.documentElement.style.setProperty(
    "--ncpc-def-board-color",
    "#00000000"
  );
  document.documentElement.style.setProperty(
    "--ncpc-def-box-color",
    "#00000000"
  );
  document.documentElement.style.setProperty("--ncpc-box-visibility", "hidden");
  //decactivation du raceway
  document.documentElement.style.setProperty(
    "--ncpc-def-Neon-race-color",
    "#00000000"
  );
  document.documentElement.style.setProperty(
    "--ncpc-defStandBase-color",
    "#00000000"
  );
  //decactivation du stand
  document.documentElement.style.setProperty(
    "--ncpc-stand-visibility",
    "hidden"
  );
}
function handleSetNeonBoardColor(color) {
  //activation du board

  document.documentElement.style.setProperty(
    "--ncpc-def-board-color",
    hexToRgb(color)
  );
  //decactivation du raceway
  document.documentElement.style.setProperty("--ncpc-visibility", "hidden");
  document.documentElement.style.setProperty("--ncpc-one-visibility", "hidden");
  //decactivation du cut to shape
  document.documentElement.style.setProperty(
    "--ncpc-cutToShape-color",
    "#00000000"
  );
  //decactivation du box
  document.documentElement.style.setProperty(
    "--ncpc-def-box-color",
    "#00000000"
  );
  document.documentElement.style.setProperty("--ncpc-box-visibility", "hidden");
  //decactivation du stand
  document.documentElement.style.setProperty(
    "--ncpc-defStandBase-color",
    "#00000000"
  );
  document.documentElement.style.setProperty(
    "--ncpc-stand-visibility",
    "hidden"
  );
}
function handleSetNeonBoxColor(color) {
  //activation du box
  document.documentElement.style.setProperty(
    "--ncpc-def-box-color",
    hexToRgb(color)
  );
  document.documentElement.style.setProperty(
    "--ncpc-box-visibility",
    "visible"
  );
  document.documentElement.style.setProperty(
    "--ncpc-def-board-color",
    hexToRgb(color)
  );
  //decactivation du raceway
  document.documentElement.style.setProperty("--ncpc-visibility", "hidden");
  document.documentElement.style.setProperty("--ncpc-one-visibility", "hidden");
  //decactivation du cut-to-shape
  document.documentElement.style.setProperty(
    "--ncpc-cutToShape-color",
    "#00000000"
  );
  //decactivation stand
  document.documentElement.style.setProperty(
    "--ncpc-defStandBase-color",
    "#00000000"
  );
  document.documentElement.style.setProperty(
    "--ncpc-stand-visibility",
    "hidden"
  );
}
function handleSetNeonRacewayColor(color) {
  //activation du raceway
  document.documentElement.style.setProperty(
    "--ncpc-def-Neon-race-color",
    color + "90"
  );
  document.documentElement.style.setProperty("--ncpc-visibility", "visible");
  document.documentElement.style.setProperty("--ncpc-one-visibility", "hidden");
  //desactivation du board et du box
  document.documentElement.style.setProperty(
    "--ncpc-def-board-color",
    "#00000000"
  );
  document.documentElement.style.setProperty(
    "--ncpc-def-box-color",
    "#00000000"
  );
  document.documentElement.style.setProperty("--ncpc-box-visibility", "hidden");
  //desactivation du cut-to-shape
  document.documentElement.style.setProperty(
    "--ncpc-cutToShape-color",
    "#00000000"
  );
  //desactivation du stand
  document.documentElement.style.setProperty(
    "--ncpc-defStandBase-color",
    "#00000000"
  );
  document.documentElement.style.setProperty(
    "--ncpc-stand-visibility",
    "hidden"
  );
}
function handleSetNeonStandColor(color) {
  //activation du stand
  document.documentElement.style.setProperty(
    "--ncpc-stand-visibility",
    "visible"
  );
  document.documentElement.style.setProperty(
    "--ncpc-defStandBase-color",
    color + "90"
  );
  document.documentElement.style.setProperty(
    "--ncpc-def-board-color",
    hexToRgb(color)
  );
  //desactivation du box
  document.documentElement.style.setProperty(
    "--ncpc-def-box-color",
    "#00000000"
  );
  document.documentElement.style.setProperty("--ncpc-box-visibility", "hidden");
  //desactivation du raceway
  document.documentElement.style.setProperty("--ncpc-visibility", "hidden");
  document.documentElement.style.setProperty("--ncpc-one-visibility", "hidden");
  //desactivation du cut-to-shape
  document.documentElement.style.setProperty(
    "--ncpc-cutToShape-color",
    "#00000000"
  );
}

function handleOff() {
  handleApplyNeonColor(["transparent"]);
  document.documentElement.style.setProperty("--ncpc-pre-light", "#00000000");
}
//fonction pour eteindre et allumer le shadow
function handleOffShadow(color) {
  const currentLightValue = getComputedStyle(document.documentElement)
    .getPropertyValue("--ncpc-light")
    .trim();
  document.documentElement.style.setProperty("--ncpc-light", color);
  document.documentElement.style.setProperty("--ncpc-pre-light", "white");
}

var changeColeur;
var delay = 1000;
let index = 0;
function handleApplyNeonColor(color, reset) {
  if (reset) {
    index = 0;
    clearInterval(changeColeur);
  }

  clearInterval(changeColeur);
  if (color.length > 1) {
    changeColeur = setInterval(() => {
      document.documentElement.style.setProperty("--ncpc-light", color[index]);
      document.documentElement.style.setProperty("--ncpc-pre-light", "white");
      if (index < color.length - 1) {
        index++;
      } else {
        index = 0;
      }
    }, delay);
  } else {
    document.documentElement.style.setProperty("--ncpc-light", color[0]);
    document.documentElement.style.setProperty("--ncpc-pre-light", "white");
  }
}
//Neon
var changeColeurText;
var delayText = 1000;
let indexText = 0;
function handleApplyTextColor(color, reset) {
  if (reset) {
    indexText = 0;
    clearInterval(changeColeurText);
  }

  clearInterval(changeColeurText);
  if (color.length > 1) {
    changeColeurText = setInterval(() => {
      document.documentElement.style.setProperty(
        "--ncpc-neon-text-color",
        color[indexText]
      );
      if (indexText < color.length - 1) {
        indexText++;
        // console.log("index")
      } else {
        indexText = 0;
      }
    }, delayText);
  } else {
    document.documentElement.style.setProperty(
      "--ncpc-neon-text-color",
      color[0]
    );
  }
}

async function downloadDivAsImage(divId, imageType) {
  var divToConvert = document.getElementById(divId);

  if (!divToConvert) {
    console.error("La div spécifiée n'existe pas.");
    return;
  }

  // Utilisez html2canvas pour capturer le contenu de la div
  // html2canvas(divToConvert, { width: divToConvert.scrollWidth, height: divToConvert.clientHeight }).then(function(canvas) {
  //   // Obtenez les données de l'image en fonction du type spécifié
  //   var imageData;
  //   switch (imageType) {
  //     case "png":
  //       imageData = canvas.toDataURL("image/png");
  //       break;
  //     case "jpeg":
  //       imageData = canvas.toDataURL("image/jpeg");
  //       break;
  //     default:
  //       console.error("Type d'image non pris en charge : " + imageType);
  //       return;
  //   }

  //   // Créez un lien de téléchargement
  //   var downloadLink = document.createElement("a");
  //   downloadLink.href = imageData;
  //   downloadLink.download = "image." + imageType;

  //   // Cliquez sur le lien pour déclencher le téléchargement automatique
  //   downloadLink.click();
  // });
  html2canvas(divToConvert).then(function (canvas) {
    window.getCanvas = canvas;

    // Obtenez les données de l'image en tant que base64 (image/png)
    // var imageData = getCanvas.toDataURL("image/png");

    var imageData;
    switch (imageType) {
      case "png":
        imageData = getCanvas.toDataURL("image/png");
        break;
      case "jpeg":
        imageData = getCanvas.toDataURL("image/jpeg");
        break;
      default:
        console.error("Type d'image non pris en charge : " + imageType);
        return;
    }

    // Créez un lien de téléchargement
    var downloadLink = document.createElement("a");
    downloadLink.href = imageData;
    // downloadLink.href = previewImgUrl;

    // Générez un numéro aléatoire pour le nom de fichier
    const lowest = 100;
    const highest = 9999999;
    var randomNumber = parseInt(Math.random() * (highest - lowest) + lowest);

    // Définissez le nom de fichier pour le téléchargement
    downloadLink.download = "custom_neon_" + randomNumber + ".png";

    // Ajoutez le lien de téléchargement à la page
    document.body.appendChild(downloadLink);

    // Cliquez sur le lien pour déclencher le téléchargement automatique
    downloadLink.click();
  });
}

// function downloadDivAsImage(divId, imageType) {
//     // Obtenez la div à convertir
//     var divToConvert = document.getElementById(divId);

//     if (!divToConvert) {
//         console.error("La div spécifiée n'existe pas.");
//         return;
//     }

//     // Capturez le contenu de la div
//     var divContent = divToConvert.innerHTML;

//     // Créez un canvas
//     var canvas = document.createElement("canvas");
//     var canvasContext = canvas.getContext("2d");

//     // Créez une image
//     var img = new Image();
//     img.src = "data:image/svg+xml," + encodeURIComponent(divContent);

//     if(img.complete){
//         console.log("Image loaded")
//     }else{
//         console.log("Image not loaded");

//         img.onload = function() {
//             // Définissez la taille du canvas en fonction de l'image
//             canvas.width = img.width;
//             canvas.height = img.height;

//             // Dessinez l'image sur le canvas
//             canvasContext.drawImage(img, 0, 0);

//             console.log("dfdfdfdffd", img.src)

//             // Obtenez les données de l'image en fonction du type spécifié
//             var imageData;
//             switch (imageType) {
//             case "svg":
//                 imageData = canvas.toDataURL("image/svg+xml");
//                 break;
//             case "png":
//                 imageData = canvas.toDataURL("image/png");
//                 break;
//             case "jpeg":
//                 imageData = canvas.toDataURL("image/jpeg");
//                 break;
//             default:
//                 console.error("Type d'image non pris en charge : " + imageType);
//                 return;
//             }

//             // Créez un lien de téléchargement
//             var downloadLink = document.createElement("a");

//             // Définissez le texte du lien (facultatif)
//             downloadLink.textContent = "Télécharger l'image";

//             // Définissez l'URL du lien avec les données de l'image
//             downloadLink.href = imageData;

//             // Définissez l'attribut de téléchargement pour indiquer que c'est un téléchargement
//             downloadLink.download = "image." + imageType;

//             // Ajoutez le lien à la page (par exemple, à la div à convertir)
//             divToConvert.appendChild(downloadLink);
//         };
//     }

//     // console.log("dfdfdfdffd", img.src)

//     // Attendez que l'image soit chargée
//     // img.onload = function() {
//     //     // Définissez la taille du canvas en fonction de l'image
//     //     canvas.width = img.width;
//     //     canvas.height = img.height;

//     //     // Dessinez l'image sur le canvas
//     //     canvasContext.drawImage(img, 0, 0);

//     //     console.log("dfdfdfdffd", img.src)

//     //     // Obtenez les données de l'image en fonction du type spécifié
//     //     var imageData;
//     //     switch (imageType) {
//     //     case "svg":
//     //         imageData = canvas.toDataURL("image/svg+xml");
//     //         break;
//     //     case "png":
//     //         imageData = canvas.toDataURL("image/png");
//     //         break;
//     //     case "jpeg":
//     //         imageData = canvas.toDataURL("image/jpeg");
//     //         break;
//     //     default:
//     //         console.error("Type d'image non pris en charge : " + imageType);
//     //         return;
//     //     }

//     //     // Créez un lien de téléchargement
//     //     var downloadLink = document.createElement("a");

//     //     // Définissez le texte du lien (facultatif)
//     //     downloadLink.textContent = "Télécharger l'image";

//     //     // Définissez l'URL du lien avec les données de l'image
//     //     downloadLink.href = imageData;

//     //     // Définissez l'attribut de téléchargement pour indiquer que c'est un téléchargement
//     //     downloadLink.download = "image." + imageType;

//     //     // Ajoutez le lien à la page (par exemple, à la div à convertir)
//     //     divToConvert.appendChild(downloadLink);
//     // };
// }

function downlImg(divId) {
  const canvas = document.getElementById("myCanvas");
  const ctx = canvas.getContext("2d");
  const downloadLink = document.getElementById("downloadLink");
  const divElement = document.getElementById(divId);

  const elementToRemove = document.getElementById("reviewImg");

  // Vérifiez si l'élément à supprimer existe avant de le supprimer
  if (elementToRemove) {
    divElement.removeChild(elementToRemove);
  }

  // Obtenir les dimensions de la div
  const width = divElement.offsetWidth;
  const height = divElement.offsetHeight;

  // Appliquer les dimensions au canevas
  canvas.width = width;
  canvas.height = height;

  // Rendre la div sur le canevas
  const divImage = new Image();
  divImage.src =
    "data:image/svg+xml," +
    encodeURIComponent(
      '<svg xmlns="http://www.w3.org/2000/svg" width="' +
        width +
        '" height="' +
        height +
        '">' +
        '<foreignObject width="100%" height="100%">' +
        divElement.outerHTML +
        "</foreignObject>" +
        "</svg>"
    );

  divImage.onload = function () {
    ctx.drawImage(divImage, 0, 0);

    // Convertir le canevas en blob
    canvas.toBlob(function (blob) {
      // Créer un objet URL à partir du blob
      const url = URL.createObjectURL(blob);

      // Attribuer l'URL au lien de téléchargement
      downloadLink.href = url;
      downloadLink.style.display = "block";

      // Simuler un clic sur le lien pour déclencher le téléchargement
      downloadLink.click();
    }, "image/png"); // Vous pouvez spécifier un type MIME différent si nécessaire
  };
}

function setScrollColor(color) {
  console.log(color, "setScrollColor");
  document.documentElement.style.setProperty("--ncpc-scrollColor", color);
}
