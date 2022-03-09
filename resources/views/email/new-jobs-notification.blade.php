<style>
    @import url('https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Pacifico&display=swap');

    .remote {
        font-family: Pacifico, cursive;
        text-transform: lowercase;
        color: #111827;
    }

    a {
        color: inherit;
        text-decoration: inherit;
    }

    .body {
        background-color: white;
    }

    .job {
        text-align: left;
        width: 80%;
        margin: 32px;
        max-width: 700px;
    }

    .job-title {
        font-weight: bold;
        font-size: 16px;
        margin-bottom: 4px;
    }
    .company{
        margin:0;
    }

    .view-button {
        width: 100%;
        text-align: center;
        display: block;
        background-color: #EF4444;
        color: white;
        padding: 8px 0;
        margin-top: 5px;
    }
    
    .job-info > p {
        margin: 1px;
    }
    .job-info {
        margin-top: 5px;
    }

</style>

<div align="center" style="font-weight: bold; text-align: center; font-family: 'Nunito', sans-serif; color: #EF4444;font-size:32px">
    <a href="https://laravelremote.com">Laravel <span style="font-family: Pacifico, cursive; text-transform: lowercase; color: #111827;">Remote</span></a>
</div>
<div align="center" class="body">
@foreach($jobs as $job)
    <div class="job">
        <p class="job-title">{{$job->position}}</p>
        <p class="company">{{$job->company}}</p>
        <div class="job-info">
            @if($job->location)
                <p><b>Location:</b> {{$job->location}}</p>
            @endif
            @if($job->salary_range->isNotEmpty())
                <p><b>Salary range:</b> {{$job->salary_range}}</p>
            @endif
            @if($job->job_type)
                <p>{{ucfirst($job->job_type)}}</p>
            @endif
        </div>
        <a href="{{ $job->source_url }}" class="view-button">View job</a>
    </div>
@endforeach
</div>
