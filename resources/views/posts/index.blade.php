<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Sync Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container py-5">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h2 class="h5 mb-0">External API Posts</h2>
                <form id="sync-form" action="{{ route('posts.sync') }}" method="POST">
                    @csrf
                    <button type="submit" id="sync-button" class="btn btn-primary d-flex align-items-center">
                        <i class="bi bi-cloud-download me-2"></i> Sync New Posts
                    </button>
                </form>
            </div>

            <div class="card-body">
                <div id="status-alert" class="alert d-none" role="alert">
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col" style="width: 80px;">SL</th>
                                <th scope="col" style="width: 80px;">Ext ID</th>
                                <th scope="col">Title</th>
                                <th scope="col">Content Preview</th>
                            </tr>
                        </thead>
                        <tbody>

                            @forelse($posts as $post)
                            <tr>
                                <td class="fw-bold text-muted">
                                    {{ ($posts->currentPage() - 1) * $posts->perPage() + $loop->iteration }}
                                </td>
                                <td class="fw-bold text-muted">{{ $post->external_id }}</td>
                                <td>{{ Str::headline($post->title) }}</td>
                                <td>
                                    <span class="text-secondary small">
                                        {{ Str::limit($post->body, 70) }}
                                    </span>
                                </td>
                            </tr>

                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">
                                    No records found. Click "Sync" to fetch data.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $posts->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
<script>
    document.getElementById('sync-form').addEventListener('submit', async function(e) {
        e.preventDefault(); // Stop standard form submission

        const btn = document.getElementById('sync-button');
        const alertBox = document.getElementById('status-alert');
        const csrfToken = document.querySelector('input[name="_token"]').value;

        // 1. Initial UI State: Loading
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Processing...';
        alertBox.classList.remove('d-none', 'alert-success', 'alert-danger');
        alertBox.classList.add('alert-info');
        alertBox.innerHTML = '<strong>Request Sent:</strong> Initializing background data sync...';

        try {
            // 2. Trigger the Sync via JSON POST
            const response = await fetch(this.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) throw new Error('Server returned an error.');

            // 3. Start Polling the /sync-status endpoint
            const checkStatus = setInterval(async () => {
                const statusRes = await fetch('/sync-status');
                const data = await statusRes.json();

                if (data.status === 'finished') {
                    clearInterval(checkStatus); // Stop asking the server

                    // 4. Update UI to success and start 10s countdown
                    alertBox.classList.replace('alert-info', 'alert-success');
                    let countdown = 10;
                    const timer = setInterval(() => {
                        alertBox.innerHTML = `<strong>Success!</strong> Data fetched and stored. Page will refresh in <b>${countdown}</b> seconds...`;
                        countdown--;

                        if (countdown < 0) {
                            clearInterval(timer);
                            window.location.reload(); // Final reload to show data
                        }
                    }, 1000);
                }
            }, 2000); // Poll every 2 seconds

        } catch (error) {
            // Handle Errors
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-cloud-download me-2"></i> Sync New Data';
            alertBox.classList.replace('alert-info', 'alert-danger');
            alertBox.innerText = "Error: " + error.message;
        }
    });
</script>

</html>