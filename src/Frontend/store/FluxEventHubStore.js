/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian.bosch.27@gmail.com>
 * (c) Ben Bieler <benjaminbieler2014@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

'use strict';

import ListenableStore from './ListenableStore';
import AppDispatcher from '../dispatcher/AppDispatcher';
import invariant from 'invariant';

/**
 * Store which registers all events automatically and generates the subscription code for any dispatcher interaction.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 * @abstract
 */
export default class FluxEventHubStore extends ListenableStore {
  /**
   * Initializes the store by registering a dispatching callback configuring the event interaction.
   *
   * @returns {this} Fluent interface.
   */
  init() {
    const config = this.getSubscribedEvents();
    if ('undefined' !== typeof config) {
      AppDispatcher
        .register((payload) => {
          invariant(
            'undefined' !== typeof payload.event,
            'Missing parameter "event" on dispatching payload!'
          );

          config.forEach(event => {
            if (payload.event === event.name) {
              const params           = event.params;
              let callbackParameters = [];
              if ('undefined' !== typeof params) {
                callbackParameters = params.map(name => {
                  invariant(
                    'undefined' !== typeof payload[name],
                    'Parameter "%s" is missing in event payload!',
                    name
                  );

                  return payload[name];
                });
              }

              invariant(
                'undefined' !== typeof event.callback,
                'Missing parameter "callback" on event payload!'
              );

              event.callback(...callbackParameters);
            }
          });
        });
    }

    return this;
  }

  /**
   * Returns a list of subscribed events including some configuration containing the following schema:
   *
   * [
   *   {
   *     "name": "event_payload_name",
   *     "callback": () => { do sth. here },
   *     "params": [
   *       "foo",  // those parameters must be present as object keys in the request payload if the request
   *       "bar"   // will be triggered
   *     ]
   *   }
   * ]
   *
   * @returns {Array.<Object>} Subscribed events.
   */
  getSubscribedEvents() {
  }
}
