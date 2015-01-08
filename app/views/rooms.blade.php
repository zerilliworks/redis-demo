@extends('layout')

@section('body')
<div class="container" ng-controller="RoomController">
    <div class="row">
        <div class="col-xs-12">
            <div class="panel panel-default" ng-repeat="room in rooms">
                <div class="panel-heading">
                    <h3 class="panel-title">Panel title</h3>
                </div>
                <div class="panel-body">
                    Panel content
                </div>
            </div>
        </div>
    </div>
</div>