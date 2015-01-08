@extends('layout')

@section('body')
<div class="container" ng-controller="QueryController">
    <h1>Run Redis Queries</h1>
    <div class="row">
        <div class="col-xs-12 col-md-8">
            <div class="panel panel-default">
                <div class="panel-body">
                    <form ng-submit="submitQuery()">
                        {{ Form::token() }}
                        <div class="form-group has-@{{lastQuery.status}}">
                            {{ Form::label('query', 'Redis Query') }}
                            {{ Form::text('query', null, ['id' => 'query-box', 'class' => 'form-control', 'ng-model' => 'queryString',
                            'typeahead' => 'command for command in commands | filter:$viewValue | limitTo: 15',
                            'autocomplete' => 'off',
                            'autocorrect' => 'off',
                            'autocapitalize' => 'off',
                            'autofocus'=>'true']) }}
                        </div>
                        <div class="alert alert-danger alert-dismissible" ng-if="lastQuery.status == 'error'">
                            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                            @{{ lastQuery.message }}
                        </div>
                        <div id="scripter" class="collapse">
                            <div ui-ace="{mode:'lua'}" ng-model="scriptBody"></div>
                        </div>
                        <div class="form-group">
                            {{ Form::submit('Submit', ['class' => 'btn btn-success', 'data-loading-text' => 'Running...', 'id' => 'submit-query-button']) }}
                            <button type="button" class="btn btn-info" data-toggle="collapse" data-target="#scripter" ng-click="toggleScripter()">
                                Script Editor
                            </button>
                            <a class="btn btn-link pull-right" href="http://redis.io/commands/eval" target="_blank" ng-show="useScript">Redis Lua Scripting Documentation</a>
                        </div>

                    </form>
                </div>
            </div>
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">Results</h3>
                </div>
                <div class="panel-body">
                    <ul class="results">
                        <li ng-repeat="(key,result) in results">
                            <span class="text-muted">127.0.0.1:6379> </span><strong class="text-primary">@{{ result.query }}</strong>
                            <ul class="nested">
                                <li ng-repeat="(key,subset) in result.data track by $index" ng-if="isNested(result.data)">
                                    @{{ key }}: <span ng-if="!isNested(subset)">@{{ subset }}</span>
                                    <ul class="nested" ng-if="isNested(subset)">
                                        <li ng-repeat="(key, subset) in subset track by $index">
                                            @{{ key }}: @{{ subset }}
                                        </li>
                                    </ul>
                                </li>
                                <li ng-if="!isNested(result.data)">@{{ result.data }}</li>
                            </ul>
                        </li>
                    </ul>
                    <p class="lead text-muted text-center" ng-show="!results.length">Command output will be shown here</p>
                </div>
            </div>
        </div>
        <div class="col-x2-12 col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Recent Commands <a class="btn btn-sm btn-default" href="#" ng-click="clearHistory()">Clear</a></h3>
                </div>
                <div class="panel-body" style="max-height: 80vh; overflow-y: scroll; -webkit-overflow-scrolling: touch;">
                    <ul>
                        <li class="command" ng-repeat="command in commandHistory track by $index | limitTo: 15">
                            <code ng-click="fillQueryBox(command)">@{{command}}</code>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">

        </div>
    </div>
</div>
<footer>
    <p class="text-center text-muted">A hacky little program by Armand for a demo with the Ithaca Web People.</p>
</footer>
<script type="text/javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/angular-ui-bootstrap/0.10.0/ui-bootstrap-tpls.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.1.3/ace.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.1.3/mode-lua.js" type="text/javascript"></script>
<script src="/js/main.js" type="text/javascript"></script>
<script src="/js/ui-ace.min.js" type="text/javascript"></script>
@stop