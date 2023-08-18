<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>HTML Constructor</title>
<style>
body {
  font-family: Roboto, sans-serif;
  line-height: 1.6;
  color: #333;
  margin: 0;
  padding: 20px;
  background-color: #ADD8E6;
}
#toolbox {
  width: 200px;
  height: 400px;
  border: 1px solid #ccc;
  float: left;
  padding: 10px;
  overflow-y: auto;
  border-radius: 10px;
  background-color: #f0f0f0;
}
#toolbox button {
  display: block;
  margin: 5px 0;
  background: linear-gradient(to bottom, #3498db, #2980b9);
  color: white;
  border: none;
  border-radius: 5px;
  padding: 8px 12px;
  cursor: pointer;
}
#workspace {
  width: 600px;
  height: 400px;
  border: 1px solid #ccc;
  float: left;
  padding: 10px;
  overflow-y: auto;
  border-radius: 10px;
  background-color: #f9f9f9;
}
.space {
  width: 600px;
  height: 400px;
  border: 1px solid #ccc;
  float: left;
  padding: 10px;
  overflow-y: auto;
  border-radius: 10px;
  background-color: #f0f0f0;
}
.editable {
  cursor: pointer;
  border: 1px solid #ccc;
  padding: 5px;
  margin-bottom: 5px;
  position: relative;
  border-radius: 10px;
  background-color: white;
}
.element-title {
  font-weight: bold;
}
.editable-buttons {
  position: absolute;
  top: 0;
  right: 0;
  display: none;
}
.editable:hover .editable-buttons {
  display: block;
}
#preview {
  width: 600px;
  height: 400px;
  border: 1px solid #ccc;
  float: left;
  padding: 10px;
  overflow: auto;
  border-radius: 10px;
  background-color: #f9f9f9;
}
.simple-button {
  padding: 5px 14px;
  font-size: 14px;
  border-radius: 5px;
  background-color: #3498db;
  color: #fff;
  text-align: center;
  cursor: pointer;
  transition: background-color 0.3s, transform 0.2s;
}
.simple-button:hover {
  background-color: #2980b9;
}
textarea {
  border: 1px solid #ccc;
  border-radius: 5px;
  box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
  resize: vertical;
  width: 100%;
}
select {
  padding: 6px;
  font-size: 14px;
  border: 1px solid #ccc;
  border-radius: 4px;
  outline: none;
  cursor: pointer;
}
select:hover {
  border-color: #999;
}
select:focus {
  border-color: #333;
}
input {
  padding: 4px;
  border: 2px solid #ccc;
  border-radius: 5px;
  font-size: 15px;
}
</style>
<script>
  let selectedElement = null;
  function allowDrop(event) {
    event.preventDefault();
  }
function editElement(element) {
  selectedElement = element;
  const titleElement = element.querySelector('.element-title');
  const alignmentOption = element.getAttribute('data-alignment');
  const widthOption = element.getAttribute('data-width') || '';
  const heightOption = element.getAttribute('data-height') || '';
  const backgroundOption = element.getAttribute('data-background') || '';
  const backimageOption = element.getAttribute('data-backimage') || '';
  document.getElementById("elementTitle").value = titleElement.textContent;
  document.getElementById("elementAlignment").value = alignmentOption;
  document.getElementById("elementWidth").value = widthOption;
  document.getElementById("elementHeight").value = heightOption;
  document.getElementById("backgroundColor").value = backgroundOption;
  document.getElementById("backgroundImage").value = backimageOption;
  const editableContent = element.querySelector('.editable-content').innerHTML;
  const parser = new DOMParser();
  const doc = parser.parseFromString(editableContent, 'text/html');
  const elementInputs = document.getElementById("elementInputs");
  elementInputs.innerHTML = "";
  let hasEditableContent = false;
  doc.body.childNodes.forEach(node => {
    if (node.nodeType === Node.ELEMENT_NODE) {
      const tagName = node.tagName.toLowerCase();
      if (tagName === 'a' || tagName === 'p' || tagName === 'button' || tagName.startsWith('h')) {
        hasEditableContent = true;
        const content = node.innerHTML;
        const inputField = `
          <div>
            <label for="${tagName}">${tagName}:</label>
            <input type="text" id="${tagName}" value="${content}" oninput="syncInputEditor('${tagName}')">
          </div>
        `;
        elementInputs.innerHTML += inputField;
        if (tagName === 'a') {
          const hrefValue = node.getAttribute('href');
          const hrefInputField = `
            <div>
              <label for="${tagName}-href">Link Href:</label>
              <input type="text" id="${tagName}-href" value="${hrefValue}" oninput="syncInputEditorHref('${tagName}')">
            </div>
          `;
          elementInputs.innerHTML += hrefInputField;
        }
      }
    }
  });
  if (!hasEditableContent) {
    elementInputs.innerHTML = "<p>No editable content found.</p>";
  }
  const htmlEditor = document.getElementById("htmlEditor");
  htmlEditor.value = editableContent;
  document.getElementById("editor").style.display = "block";
}
function syncInputEditor(tagName) {
  const input = document.getElementById(tagName);
  const inputHref = document.getElementById(`${tagName}-href`);
  const htmlEditor = document.getElementById("htmlEditor");
  const content = input.value;
  const hrefValue = inputHref ? inputHref.value : '';
  const pattern = new RegExp(`<${tagName}.*?>([\\s\\S]*?)<\/${tagName}>`, 'i');
  const currentHtml = htmlEditor.value;
  let newHtml = currentHtml.replace(pattern, `<${tagName}${hrefValue ? ` href="${hrefValue}"` : ''}>${content}</${tagName}>`);
  if (hrefValue) {
    newHtml = newHtml.replace(new RegExp(`href=["'][^"']*["']`), `href="${hrefValue}"`);
  } else {
    newHtml = newHtml.replace(new RegExp(` href=["'][^"']*["']`), '');
  }
  htmlEditor.value = newHtml;
}
function syncInputEditorHref(tagName) {
  const inputHref = document.getElementById(`${tagName}-href`);
  const htmlEditor = document.getElementById("htmlEditor");
  const hrefValue = inputHref.value;
  const patternHref = new RegExp(`<${tagName}.*?href=["']([^"']*)["'][^>]*>`, 'i');
  const currentHtml = htmlEditor.value;
  const newHtml = currentHtml.replace(patternHref, `<${tagName} href="${hrefValue}">`);
  htmlEditor.value = newHtml;
}
function syncHtmlEditor() {
  const htmlEditor = document.getElementById("htmlEditor");
  const parsedHtml = new DOMParser().parseFromString(htmlEditor.value, 'text/html');
  const elementInputs = document.getElementById("elementInputs");
  parsedHtml.body.childNodes.forEach(node => {
    if (node.nodeType === Node.ELEMENT_NODE) {
      const tagName = node.tagName.toLowerCase();
      const input = document.getElementById(tagName);
      if (input) {
        input.value = node.innerHTML;
      }
    }
  });
}
function updateElement() {
  if (selectedElement) {
    const newTitle = document.getElementById("elementTitle").value;
    const newAlignment = document.getElementById("elementAlignment").value;
    const newWidth = document.getElementById("elementWidth").value;
    const newHeight = document.getElementById("elementHeight").value;
	const newbackground = document.getElementById("backgroundColor").value;
	const newbackimage = document.getElementById("backgroundImage").value;
    selectedElement.querySelector('.element-title').textContent = newTitle;
    selectedElement.setAttribute('data-alignment', newAlignment);
    selectedElement.setAttribute('data-width', newWidth);
    selectedElement.setAttribute('data-height', newHeight);
	selectedElement.setAttribute('data-background', newbackground);
	selectedElement.setAttribute('data-backimage', newbackimage);
    const elementInputs = document.getElementById("elementInputs");
    const inputFields = elementInputs.querySelectorAll('input');
    const editableContent = Array.from(inputFields).map(input => {
      const tagName = input.id;
      const content = input.value;
      return `<${tagName}>${content}</${tagName}>`;
    }).join('');
    selectedElement.querySelector('.editable-content').innerHTML = editableContent;
    const htmlEditor = document.getElementById("htmlEditor");
    selectedElement.querySelector('.editable-content').innerHTML = htmlEditor.value;
    document.getElementById("editor").style.display = "none";
    generateHTML();
  }
}
function generateHTML() {
  const workspace = document.getElementById("workspace");
  const elements = Array.from(workspace.querySelectorAll(".editable")).map(element => {
    const content = element.querySelector('.editable-content').innerHTML;
    const alignment = element.getAttribute('data-alignment');
    const width = element.getAttribute('data-width');
    const height = element.getAttribute('data-height');
	const background = element.getAttribute('data-background');
	const backimage = element.getAttribute('data-backimage');
    return { content, alignment, width, height, background, backimage };
  }).filter(item => item.content.trim() !== "");
  const pageTitle = document.getElementById("pageTitle").value;
  const pageDescription = document.getElementById("pageDescription").value;
  const pageKeywords = document.getElementById("pageKeywords").value;
  let html = `<!DOCTYPE html>\n<html>\n<head>\n`;
  html += `<meta charset="UTF-8">\n`;
  html += `<meta name="viewport" content="width=device-width, initial-scale=1.0">\n`;
  html += `<title>${pageTitle}</title>\n`;
  html += `<meta name="description" content="${pageDescription}">\n`;
  html += `<meta name="keywords" content="${pageKeywords}">\n`;
  html += `<style>\n${document.getElementById("userCSS").value}\n</style>\n`;
  html += "</head>\n<body>\n";
elements.forEach((item) => {
  const { content, alignment, width, height, background, backimage } = item;
  const alignmentStyle = alignment ? `text-align: ${alignment};` : '';
  const widthAttribute = width ? `width: ${width};` : '';
  const heightAttribute = height ? `height: ${height};` : '';
  const backgroundAttribute = background ? `background-color: ${background};` : '';
  const backimageAttribute = backimage ? `background-image: url('${backimage}');background-size: cover;background-repeat: no-repeat;background-position: center center;` : '';
  html += `<div style="${alignmentStyle} ${widthAttribute} ${heightAttribute} ${backgroundAttribute} ${backimageAttribute}">${content}</div>\n`;
});
  html += "</body>\n</html>";
  document.getElementById("generatedHTML").value = html;
}
function openPreviewWindow() {
  const html = document.getElementById("generatedHTML").value;
  const userCSS = document.getElementById("userCSS").value;
  const selectedCSS = document.getElementById("selectedCSS").value;
  const pageTitle = document.getElementById("pageTitle").value;
  const pageDescription = document.getElementById("pageDescription").value;
  const pageKeywords = document.getElementById("pageKeywords").value;
  const previewWindow = window.open('', '_blank');
  previewWindow.document.open();
  previewWindow.document.write(`
    <!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>${pageTitle}</title>
      <meta name="description" content="${pageDescription}">
	  <meta name="keywords" content="${pageKeywords}">
      <style>
        ${userCSS}
      </style>
      <style>
        ${selectedCSS}
      </style>
    </head>
    <body>
      ${html}
    </body>
    </html>
  `);
  previewWindow.document.close();
}
  function deleteElement(element) {
    element.remove();
    generateHTML();
  }
  function moveElementUp(element) {
    const prevElement = element.previousElementSibling;
    if (prevElement) {
      element.parentNode.insertBefore(element, prevElement);
      generateHTML();
    }
  }
  function moveElementDown(element) {
    const nextElement = element.nextElementSibling;
    if (nextElement) {
      element.parentNode.insertBefore(nextElement, element);
      generateHTML();
    }
  }
  function searchElements() {
    const searchTerm = document.getElementById("searchTerm").value.toLowerCase();
    const buttons = document.querySelectorAll("#toolbox button");
    buttons.forEach(button => {
      const buttonName = button.textContent.toLowerCase();
      if (buttonName.includes(searchTerm)) {
        button.style.display = "block";
      } else {
        button.style.display = "none";
      }
    });
  }
function loadElementFromFile(filePath) {
  fetch(filePath)
    .then(response => response.text())
    .then(elementContent => {
      const fileName = filePath.split('/').pop();
      addElement(elementContent, fileName);
    })
    .catch(error => {
      console.error("Error fetching element file:", error);
    });
}
function updatePageTitle() {
  const pageTitle = document.getElementById("pageTitle").value;
  const previewTitle = document.querySelector('#previewTitle');
  if (previewTitle) {
    previewTitle.textContent = pageTitle;
  }
}
function updatePageDescription() {
  const pageDescription = document.getElementById("pageDescription").value;
  const previewDescription = document.querySelector('#previewDescription');
  if (previewDescription) {
    previewDescription.setAttribute("content", pageDescription);
  }
}
function updatePageKeywords() {
  const pageKeywords = document.getElementById("pageKeywords").value;
  const previewKeywords = document.querySelector('#previewKeywords');
  if (previewKeywords) {
    previewKeywords.setAttribute("content", pageKeywords);
  }
}
function combineSelectedElements() {
  const selectedCheckboxes = Array.from(document.querySelectorAll('.checkbox'));
  const selectedElements = selectedCheckboxes
    .filter(checkbox => checkbox.checked)
    .map(checkbox => checkbox.parentElement.parentElement);
  if (selectedElements.length < 2) {
    alert("Select at least two elements to combine.");
    return;
  }
  const combinedContent = selectedElements.map(element => {
    return element.querySelector('.editable-content').innerHTML;
  }).join('');
  const combinedTitle = selectedElements.map(element => {
    return element.querySelector('.element-title').textContent;
  }).join('-');
  const combinedElement = document.createElement("div");
  combinedElement.className = "editable";
  combinedElement.innerHTML = `
    <div>
      <input type="checkbox" class="checkbox">
      <span class="element-title">${combinedTitle}</span>
    </div>
    <div class="editable-content" style="display: none;">${combinedContent}</div>
    <div class="editable-buttons">
      <button class="simple-button" onclick="editElement(this.parentElement.parentElement)">Edit</button>
      <button class="simple-button" onclick="deleteElement(this.parentElement.parentElement)">Delete</button>
      <button class="simple-button" onclick="moveElementUp(this.parentElement.parentElement)">Move Up</button>
      <button class="simple-button" onclick="moveElementDown(this.parentElement.parentElement)">Move Down</button>
    </div>
  `;
  selectedElements.forEach(element => {
    element.remove();
  });
  document.getElementById("workspace").appendChild(combinedElement);
  generateHTML();
}
  function updateBackgroundColor() {
    const ALLbackgroundColor = document.getElementById("ALLbackgroundColor").value;
    const userCSS = document.getElementById("userCSS");
    const updatedCSS = userCSS.value.replace(/(body\s*{[^}]*)(background-color:)[^;]+(;)([^}]*})/, `$1$2 ${ALLbackgroundColor}$3$4`);
    userCSS.value = updatedCSS;
    generateHTML();
  }
function connectElements() {
  const selectedCheckboxes = Array.from(document.querySelectorAll('.checkbox'));
  const selectedElements = selectedCheckboxes
    .filter(checkbox => checkbox.checked)
    .map(checkbox => checkbox.parentElement.parentElement);
  if (selectedElements.length < 2) {
    alert("Select at least two elements to connect.");
    return;
  }
  const connectedTable = document.createElement("table");
  connectedTable.style.borderCollapse = "collapse";
  const row = document.createElement("tr");
  selectedElements.forEach(element => {
    const cell = document.createElement("td");
    const content = element.querySelector('.editable-content').innerHTML;
    cell.innerHTML = content;
    cell.style.border = "none";
    row.appendChild(cell);
    element.remove();
  });
  connectedTable.appendChild(row);
  const combinedElement = document.createElement("div");
  combinedElement.className = "editable";
  const combinedTitle = selectedElements.map(element => {
    return element.querySelector('.element-title').textContent;
  }).join('-');
  combinedElement.innerHTML = `
    <div>
      <input type="checkbox" class="checkbox">
      <span class="element-title">${combinedTitle}</span>
    </div>
    <div class="editable-content" style="display: none;">${connectedTable.outerHTML}</div>
    <div class="editable-buttons">
      <button class="simple-button" onclick="editElement(this.parentElement.parentElement)">Edit</button>
      <button class="simple-button" onclick="deleteElement(this.parentElement.parentElement)">Delete</button>
      <button class="simple-button" onclick="moveElementUp(this.parentElement.parentElement)">Move Up</button>
      <button class="simple-button" onclick="moveElementDown(this.parentElement.parentElement)">Move Down</button>
    </div>
  `;

  document.getElementById("workspace").appendChild(combinedElement);
}
</script>
</head>
<body>
<div>
<div id="toolbox">
  <input type="text" id="searchTerm" placeholder="Search" onkeyup="searchElements()"><hr>
  <?php
    $files = scandir("data");
    foreach ($files as $file) {
      if ($file !== "." && $file !== "..") {
        echo '<button class="editable" onclick="loadElementFromFile(`data/' . $file . '`)">' . str_replace(".html", "", $file) . '</button>';
      }
    }
  ?>
</div>
<div id="workspace" ondragover="allowDrop(event)"><button class="simple-button" onclick="combineSelectedElements()">Combine Selected</button><button class="simple-button" onclick="connectElements()">In One Line Selected (table)</button>
<hr>
</div></div>
<div class="space">
<div id="headSection">
  <a>User META:</a><br>
  <label for="pageTitle">Page Title:</label>
  <input type="text" id="pageTitle" oninput="updatePageTitle()">
  <br>
  <label for="pageDescription">Page Description:</label>
  <input type="text" id="pageDescription" oninput="updatePageDescription()">
  <br>
  <label for="pageKeywords">Page Keywords:</label>
  <input type="text" id="pageKeywords" oninput="updatePageKeywords()">
  <br>
  <label for="ALLbackgroundColor">Page Background Color:</label>
  <input type="color" id="ALLbackgroundColor" onchange="updateBackgroundColor()">
</div><hr>
<div>
  <label for="userCSS">User CSS:</label><br>
  <textarea id="userCSS" rows="5" cols="50"></textarea>
</div>
<div>
  <label for="selectedCSS">Selected CSS:</label>
  <select id="selectedCSS" onchange="updateUserCSS()">
    <option value="">No CSS</option>
    <?php
      $cssFiles = scandir("datacss");
      foreach ($cssFiles as $cssFile) {
        if ($cssFile !== "." && $cssFile !== "..") {
          echo '<option value="' . $cssFile . '">' . $cssFile . '</option>';
        }
      }
    ?>
  </select>
</div><hr>
<a>User HTML:</a><br>
<button class="simple-button" onclick="generateHTML()">Generate HTML</button>
<button class="simple-button" onclick="openPreviewWindow()">Preview</button><br>
<textarea id="generatedHTML" rows="10" cols="80"></textarea>
<script>
function addElement(elementContent, fileName, alignment) {
  const element = document.createElement("div");
  element.className = "editable";
  if (alignment) {
    element.setAttribute('data-alignment', alignment);
  }
  element.innerHTML = `
    <div>
      <input type="checkbox" class="checkbox">
      <span class="element-title">${fileName}</span>
    </div>
    <div class="editable-content" style="display: none;">${elementContent}</div>
    <div class="editable-buttons">
      <button class="simple-button" onclick="editElement(this.parentElement.parentElement)">Edit</button>
      <button class="simple-button" onclick="deleteElement(this.parentElement.parentElement)">Delete</button>
      <button class="simple-button" onclick="moveElementUp(this.parentElement.parentElement)">Move Up</button>
      <button class="simple-button" onclick="moveElementDown(this.parentElement.parentElement)">Move Down</button>
    </div>
  `;
  document.getElementById("workspace").appendChild(element);
}
  function updateUserCSS() {
    const selectedCSS = document.getElementById("selectedCSS").value;
    if (selectedCSS) {
      fetch(`datacss/${selectedCSS}`)
        .then(response => response.text())
        .then(cssContent => {
          document.getElementById("userCSS").value = cssContent;
        })
        .catch(error => {
          console.error("Error fetching CSS:", error);
        });
    } else {
      document.getElementById("userCSS").value = "";
    }
  }
</script>
</div><br>
<div class="space">
<div id="editor" style="display: none;">
  <label for="elementTitle">Element Title:</label>
  <input type="text" id="elementTitle"><br><hr>
 <a><b>Element Text:</b></a><br>
  <div id="elementInputs"></div><hr>
  <div>
    <label for="htmlEditor">HTML Editor:</label><br>
    <textarea id="htmlEditor" rows="5" cols="50" oninput="syncHtmlEditor()"></textarea>
  </div>
  <a><b>Optional settings:</b></a><br>
  <div>
    <label for="elementAlignment">Element Alignment:</label>
    <select id="elementAlignment">
      <option value="left">Left</option>
      <option value="center">Center</option>
      <option value="right">Right</option>
    </select>
  </div>
<label for="elementWidth">Element Width (300px/50%):</label>
<input type="text" id="elementWidth"><br>
<label for="elementHeight">Element Height (300px/50%):</label>
<input type="text" id="elementHeight"><br>
<label for="backgroundColor">Background Color (#000):</label>
<input type="text" id="backgroundColor"><br>
<label for="backgroundImage">Background Image (URL):</label>
<input type="text" id="backgroundImage"><br>
	<button class="simple-button" onclick="updateElement()">Update Element</button>
</div></div>
</body>
</html>
