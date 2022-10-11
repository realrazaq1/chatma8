// DOM Elements
const textInput = document.querySelector("#text"),
  fileInput = document.querySelector("#file"),
  postBtn = document.querySelector("#post_btn"),
  feedback = document.querySelector(".feedback"),
  uploadPreview = document.querySelector(".upload-preview"),
  likeBtns = document.querySelectorAll(".like-btn"),
  unLikeBtns = document.querySelectorAll(".unlike-btn"),
  commentBtns = document.querySelectorAll(".comment-btn"),
  commentsSection = document.querySelectorAll(".comments-section"),
  addFriendBtn = document.querySelector(".addfriend-btn"),
  acceptFriendBtn = document.querySelector(".accept-friend"),
  rejectFriendBtn = document.querySelector(".reject-friend"),
  addFriendIcons = document.querySelectorAll(".addfriend-icon"),
  unFriendBtn = document.querySelector(".unfriend"),
  cancelBtn = document.querySelector(".cancel");

// Vars
const ajaxUrl = "http://localhost/chatma8/php/ajax.php";
let commentsTotal,
  file = null;

// Events
postBtn && postBtn.addEventListener("click", uploadPost);
addFriendBtn && addFriendBtn.addEventListener("click", addFriend);
acceptFriendBtn && acceptFriendBtn.addEventListener("click", acceptFriend);
rejectFriendBtn && rejectFriendBtn.addEventListener("click", rejectFriend);
unFriendBtn && unFriendBtn.addEventListener("click", unFriend);
cancelBtn && cancelBtn.addEventListener("click", cancelFile);
fileInput && fileInput.addEventListener("change", preview);
for (const btn of likeBtns) {
  btn.addEventListener("click", () => likePost(btn));
}
for (const btn of unLikeBtns) {
  btn.addEventListener("click", () => unLikePost(btn));
}
for (const btn of commentBtns) {
  btn.addEventListener("click", () => comment(btn));
}
for (const btn of addFriendIcons) {
  btn.addEventListener("click", () => addFriendByIcon(btn));
}
document.addEventListener("DOMContentLoaded", () => {
  setInterval(() => {
    syncComments();
  }, 1000);
});

// Functions

function cancelFile() {
  file = null;
  fileInput.value = null;
  uploadPreview.innerHTML = "";
  cancelBtn.style.display = "none";
}

async function addFriend(e) {
  const userId = e.target.dataset.userId;

  const form = new FormData();
  form.append("user_id", userId);

  const res = await fetch(`${ajaxUrl}?addfriend`, {
    method: "POST",
    body: form,
  });
  const resp = await res.json();
  resp.msg && location.reload();
  resp.error && alert(resp.error);
}

async function addFriendByIcon(btn) {
  const userId = btn.dataset.userId;
  console.log(userId);

  const form = new FormData();
  form.append("user_id", userId);

  const res = await fetch(`${ajaxUrl}?addfriend`, {
    method: "POST",
    body: form,
  });
  const resp = await res.json();
  resp.msg && alert("Friend Request Sent!");
  resp.msg && location.reload();
  resp.error && alert(resp.error);
}

async function acceptFriend(e) {
  const userId = e.target.dataset.userId;

  const form = new FormData();
  form.append("user_id", userId);

  const res = await fetch(`${ajaxUrl}?acceptfriend`, {
    method: "POST",
    body: form,
  });
  const resp = await res.json();
  resp.msg && location.reload();
  resp.error && console.log(resp.error);
  resp.error && alert(resp.error);
}

async function rejectFriend(e) {
  const userId = e.target.dataset.userId;

  const form = new FormData();
  form.append("user_id", userId);

  const res = await fetch(`${ajaxUrl}?rejectfriend`, {
    method: "POST",
    body: form,
  });
  const resp = await res.json();
  resp.msg && location.reload();
  resp.error && console.log(resp.error);
  resp.error && alert(resp.error);
}

async function unFriend(e) {
  const userId = e.target.dataset.userId;
  console.log(userId);

  const form = new FormData();
  form.append("user_id", userId);

  const res = await fetch(`${ajaxUrl}?unfriend`, {
    method: "POST",
    body: form,
  });
  const resp = await res.json();
  resp.msg && location.reload();
  resp.error && console.log(resp.error);
  resp.error && alert(resp.error);
}

async function unFollowUser(e) {
  const userId = e.target.dataset.userId;

  const form = new FormData();
  form.append("user_id", userId);

  const res = await fetch(`${ajaxUrl}?unfollow`, {
    method: "POST",
    body: form,
  });
  const resp = await res.json();
  resp.msg && location.reload();
  resp.error && alert(resp.error);
}

async function syncComments() {
  for (const el of commentsSection) {
    const postId = el.dataset.postId;
    const form = new FormData();
    form.append("post_id", postId);

    const res = await fetch(`${ajaxUrl}?sync_comments`, {
      method: "POST",
      body: form,
    });
    const resp = await res.json();
    commentsTotal = resp.length;
    const comments = resp.splice(0, 5);
    const cSection = document.querySelector(`#comments-section${postId}`);
    cSection.innerHTML = "";
    for (const comment of comments) {
      const commentCard = `<div class="comment-card">
            <div>
                <a href="profile.php?u=${comment.username}">
                    <img src="./assets/img/profile/${comment.profile_pic}" alt="User Profile Picture">
                </a>
            </div>
            <div class="comment-text">
                <h4 class="ct-heading"><a href="profile.php?u=${comment.username}">${comment.firstname} ${comment.lastname}</a></h4>
                <p class="ct-p">
                ${comment.text}
                </p>
    </div>`;
      const cSection = document.querySelector(`#comments-section${postId}`);
      cSection.innerHTML += commentCard;
    }
  }
}

async function comment(btn) {
  const postId = btn.dataset.postId;
  const text = document.querySelector(`#comment${postId}`).value;
  const form = new FormData();
  form.append("post_id", postId);
  form.append("text", text);

  const res = await fetch(`${ajaxUrl}?comment`, {
    method: "POST",
    body: form,
  });
  const resp = await res.json();
  resp.error && alert(resp.error);
  if (resp.msg == "comment sent") {
    document.querySelector(`#comment${postId}`).value = "";
    let commentCountEl = document.querySelector(`#comment-count${postId}`);
    let commentCount = Number(commentCountEl.innerText);
    commentCountEl.innerText = commentsTotal;
    commentCountEl.innerText = commentCount + 1;
  }
}

async function likePost(btn) {
  const postId = btn.dataset.postId;
  const form = new FormData();
  form.append("post_id", postId);
  const res = await fetch(`${ajaxUrl}?like_post`, {
    method: "POST",
    body: form,
  });
  const resp = await res.json();
  if (resp.msg == "post liked") {
    console.log(resp);
    btn.style.display = "none";
    document.querySelector(`#unlike${postId}`).style.display = "inline";
    let likeCountEl = document.querySelector(`#like-count${postId}`);
    let likeCount = Number(likeCountEl.innerText);
    likeCountEl.innerText = likeCount + 1;
  }
}

async function unLikePost(btn) {
  const postId = btn.dataset.postId;
  const form = new FormData();
  form.append("post_id", postId);
  const res = await fetch(`${ajaxUrl}?unlike_post`, {
    method: "POST",
    body: form,
  });
  const resp = await res.json();
  if (resp.msg == "post unliked") {
    console.log(resp);
    btn.style.display = "none";
    document.querySelector(`#like${postId}`).style.display = "inline";
    let likeCountEl = document.querySelector(`#like-count${postId}`);
    let likeCount = Number(likeCountEl.innerText);
    likeCountEl.innerText = likeCount - 1;
  }
}

async function uploadPost() {
  file = fileInput.files[0];
  let postText = textInput.value;
  const form = new FormData();
  form.append("text", postText);
  form.append("file", file);
  const res = await fetch(`${ajaxUrl}?post_upload`, {
    method: "POST",
    body: form,
  });
  const resp = await res.json();
  if (resp.msg) {
    feedback.innerHTML = `<p class='success'> ${resp.msg} </p>`;
    location.reload();
  }
  if (resp.error) {
    feedback.innerHTML = `<p class='error'> ${resp.error} </p>`;
  }
}

// show image/video preview
function preview() {
  file = this.files[0];
  let fileType = file.type.split("/")[0];
  if (file) {
    if (fileType == "image") {
      const imageEl = document.createElement("img");
      imageEl.setAttribute("src", URL.createObjectURL(file));
      uploadPreview.innerHTML = "";
      uploadPreview.appendChild(imageEl);
      document.querySelector(".cancel").style.display = "flex";
    } else if (fileType == "video") {
      const videoEl = document.createElement("video");
      videoEl.setAttribute("src", URL.createObjectURL(file));
      videoEl.setAttribute("controls", "");
      uploadPreview.innerHTML = "";
      uploadPreview.appendChild(videoEl);
      document.querySelector(".cancel").style.display = "flex";
    }
  }
}
