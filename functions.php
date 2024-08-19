<?php
// 파일 업로드 - create_post, update_post
function handleFileUpload($existing_file_path) {
    $upload_dir = UPLOAD_DIR;
    $file_path = $existing_file_path;
    $error_message = '';

    if (isset($_FILES['file']) && $_FILES['file']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $allowed_exts = ALLOWED_EXTS;
            $file_ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
            $max_file_size = MAX_FILE_SIZE; // 5MB

            if ($_FILES['file']['size'] > $max_file_size) {
                $error_message = "파일 용량 초과 (최대 5MB)";
            } elseif (!in_array($file_ext, $allowed_exts)) {
                $error_message = "허용되지 않는 파일 형식입니다.";
            } else {
                $file_name = time() . '_' . uniqid() . '_' . basename($_FILES['file']['name']);
                $new_file_path = $upload_dir . $file_name;

                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                if (move_uploaded_file($_FILES['file']['tmp_name'], $new_file_path)) {
                    // 기존 파일이 있으면 삭제
                    if ($existing_file_path && file_exists($existing_file_path)) {
                        unlink($existing_file_path);
                    }
                    $file_path = $new_file_path;
                } else {
                    $error_message = "파일 업로드 실패!";
                }
            }
        } else {
            $error_message = "파일 업로드 중 오류가 발생했습니다.";
        }
    }

    return [
        'file_path' => $file_path,
        'error_message' => $error_message,
    ];
}

// 게시물 생성 - create_post
function createPost($title, $content, $file_path) {
    global $mysqli;
    $stmt = $mysqli->prepare("INSERT INTO posts (title, content, file_path, user_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $title, $content, $file_path, $_SESSION['id']);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

// 게시물 수정 - update_post
function updatePost($id, $title, $content, $file_path) {
    global $mysqli;
    $stmt = $mysqli->prepare("UPDATE posts SET title = ?, content = ?, file_path = ? WHERE id = ?");
    $stmt->bind_param("sssi", $title, $content, $file_path, $id);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

// 게시물 데이터 가져오기 - update_post
function fetchPost($id) {
    global $mysqli;
    $stmt = $mysqli->prepare("SELECT title, content, file_path, user_id FROM posts WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $result;
}

// 게시물 삭제 - delete_post
function deletePost($mysqli, $postId, $userId) {
    // 게시물 데이터 가져오기
    $stmt = $mysqli->prepare("SELECT user_id, file_path FROM posts WHERE id = ?");
    $stmt->bind_param("i", $postId);
    $stmt->execute();
    $stmt->bind_result($post_user_id, $file_path);

    if (!$stmt->fetch()) {
        return ['success' => false, 'message' => '게시물이 존재하지 않습니다.'];
    }
    $stmt->close();

    // 게시물 작성자 확인
    if ($userId !== $post_user_id) {
        return ['success' => false, 'message' => '게시물 삭제 권한이 없습니다.'];
    }

    // 게시물 삭제
    $stmt = $mysqli->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->bind_param("i", $postId);
    $stmt->execute();
    $stmt->close();

    // 파일 삭제
    if ($file_path && file_exists($file_path)) {
        unlink($file_path);
    }

    return ['success' => true, 'message' => '게시물이 삭제되었습니다.'];
}

// 총 게시물 수 - index, search
function getTotalPosts($mysqli, $query) {
    if ($query) {
        $stmt = $mysqli->prepare("SELECT COUNT(*) FROM posts WHERE title LIKE ? OR content LIKE ?");
        $search_term = '%' . $query . '%';
        $stmt->bind_param("ss", $search_term, $search_term);
    } else {
        $stmt = $mysqli->prepare("SELECT COUNT(*) FROM posts");
    }
    $stmt->execute();
    $stmt->bind_result($total_posts);
    $stmt->fetch();
    $stmt->close();
    
    return $total_posts;
}

// 게시물 가져오기 - index, search
function getPosts($mysqli, $offset, $post_per_page, $query) {
    if ($query) {
        $stmt = $mysqli->prepare("SELECT id, title, content, created_at, updated_at FROM posts WHERE title LIKE ? OR content LIKE ? ORDER BY created_at DESC LIMIT ?, ?");
        $search_term = '%' . $query . '%';
        $stmt->bind_param("ssii", $search_term, $search_term, $offset, $post_per_page);
    } else {
    $stmt = $mysqli->prepare("SELECT posts.id, posts.title, posts.content, posts.created_at, posts.updated_at, posts.file_path, users.username 
                               FROM posts 
                               LEFT JOIN users ON posts.user_id = users.id 
                               ORDER BY posts.created_at DESC 
                               LIMIT ?, ?");
    $stmt->bind_param("ii", $offset, $post_per_page);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result;
}

// 게시물 내용 미리보기 - index, search, user_posts
function truncateContent($content, $maxLength = MAX_LENGTH) {
    return strlen($content) > $maxLength ? substr($content, 0, $maxLength) . '...' : $content;
}

// 게시물 가져오기 - read_post
function getPostById($mysqli, $id) {
    $stmt = $mysqli->prepare("SELECT title, content, file_path, created_at, updated_at, user_id FROM posts WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($title, $content, $file_path, $created_at, $updated_at, $post_user_id);

    if (!$stmt->fetch()) {
        return null; // 게시물이 존재하지 않으면 null 반환
    }

    $stmt->close();
    return [
        'title' => $title,
        'content' => $content,
        'file_path' => $file_path,
        'created_at' => $created_at,
        'updated_at' => $updated_at,
        'user_id' => $post_user_id
    ];
}

// 작성자 이름 가져오기 - read_post
function getAuthorUsername($mysqli, $post_user_id) {
    $stmt = $mysqli->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->bind_param("i", $post_user_id);
    $stmt->execute();
    $stmt->bind_result($author_username);
    $stmt->fetch();
    $stmt->close();
    return $author_username;
}

// 사용자가 작성한 게시물을 가져오기
function getUserPosts($mysqli, $user_id) {
    $stmt = $mysqli->prepare("
        SELECT id, title, content, created_at, updated_at 
        FROM posts 
        WHERE user_id = ? 
        ORDER BY created_at DESC
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    
    return $result;
}
?>
