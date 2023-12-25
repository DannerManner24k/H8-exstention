document.addEventListener('DOMContentLoaded', (event) => {
    let blogCommentButtons = document.querySelectorAll('.blog-comment-btn');
    let deletePostButtons = document.querySelectorAll('.delete-post-btn');
    //let deleteCommentButtons = document.querySelectorAll('.delete-comment-btn');

    const likeButtons = document.querySelectorAll('.like-button');
    const commentButtons = document.querySelectorAll('.post-comment-btn');
    const likeCommentButtons = document.querySelectorAll('.like-comment-button');
    const deleteCommentButtons = document.querySelectorAll('.delete-comment-btn');
    

    blogCommentButtons.forEach(button => {
        button.addEventListener('click', function() {
            let postId = this.getAttribute('data-post-id');
            toggleCommentSection(postId);
        });
    });

    deletePostButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            let formId = this.getAttribute('data-form-id');
            deletePost(event, formId);
        });
    });

    /*deleteCommentButtons.forEach(button => {
        button.addEventListener('click', function() {
            let formId = this.getAttribute('data-form-id');
            deleteComment(event, formId);
        });
    });*/

    likeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.getAttribute('data-post-id');
            const subpageSlug = this.getAttribute('data-subpage-slug');
            const postSlug = this.getAttribute('data-post-slug');
            toggleLike(postId, subpageSlug, postSlug, this);
        });
    });

    commentButtons.forEach(button => {
        button.addEventListener('click', function() {
            const form = this.closest('form');
            postComment(form);
        });
    });

    likeCommentButtons.forEach(button => {
        button.addEventListener('click', function() {
            const commentId = this.getAttribute('data-comment-id');
            toggleCommentLike(commentId, this);
        });
    });

    deleteCommentButtons.forEach(button => {
        button.addEventListener('click', function() {
            const commentId = this.getAttribute('data-comment-id');
            deleteCommentAlert(commentId, this);
        });
    });

    

});

// Toggle comment section
function toggleCommentSection(postId) {
    var commentSection = document.getElementById('comment-section-' + postId);
    if (commentSection.style.display === 'none' || commentSection.style.display === '') {
        commentSection.style.display = 'block';
    } else {
        commentSection.style.display = 'none';
    }
}

// Delete a post
function deletePost(event, formId) {
    event.preventDefault();
    Swal.fire({
        title: 'Are you sure? you want to delete this post',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel!'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(formId).submit();
        }
    });
}

// Delete a comment
function deleteCommentAlert(commentId, buttonElement) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel!'
    }).then((result) => {
        if (result.isConfirmed) {
            deleteComment(commentId, buttonElement);
        }
    });
}


// AJAX call for liking a post
function toggleLike(postId, subpageSlug, postSlug, buttonElement) {
    fetch(`/subpage/${subpageSlug}/post/${postSlug}/toggle-like`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ postId: postId })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        buttonElement.textContent = `${data.likesCount} Like`;

        if (buttonElement.classList.contains('liked')) {
            // If it does, remove 'liked' and add 'not-liked'
            buttonElement.classList.remove('liked');
            buttonElement.classList.add('not-liked');
        } else {
            // If it doesn't, add 'liked' and remove 'not-liked'
            buttonElement.classList.add('liked');
            buttonElement.classList.remove('not-liked');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

//AJAX call for posting a comment
function postComment(form) {
    const formData = new FormData(form);
    const url = form.getAttribute('action');

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.status === 'success') {
            // Clear the form input
            form.querySelector('textarea[name="content"]').value = '';
    
            const commentSection = document.querySelector('.comment-section');
            commentSection.insertAdjacentHTML('beforeend', data.commentHtml);
            }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// AJAX call for liking a comment
function toggleCommentLike(commentId, buttonElement) {
    fetch(`/comments/${commentId}/toggle-like`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        buttonElement.textContent = `${data.likesCount} Like`;
        buttonElement.classList.toggle('liked');
        buttonElement.classList.toggle('not-liked');
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

//AJAX call for deleting a comment
function deleteComment(commentId, buttonElement) {
    fetch(`/comments/${commentId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.status === 'success') {
            buttonElement.closest('.c').remove(); // Adjust the selector as needed
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
