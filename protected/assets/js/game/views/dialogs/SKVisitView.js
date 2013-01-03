/*global Backbone, _*/
(function () {
    "use strict";
    window.SKVisitView = Backbone.View.extend({
        'el':'body .visitor-container',
        'initialize':function () {
            this.render();
        },
        'render':function () {
            var replicas = this.options.event.get('data'),
                my_replicas = [],
                remote_replica;
            replicas.forEach(function (replica) {
                if (replica.ch_to === '1') {
                    remote_replica = remote_replica
                } else {
                    my_replicas.push(replica)
                }

            });
            console.log(my_replicas);
            this.$el.html(_.template($('#visit_template').html(), {
                'remote_replica':remote_replica,
                'my_replicas':my_replicas
            }));
        },
        'nextDialog':function () {

        }
    });
})();