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

import AppDispatcher from '../../dispatcher/AppDispatcher';
import FluxEventHubStore from '../../store/FluxEventHubStore';
import sinon from 'sinon';
import chai from 'chai';

describe('FluxEventHubStore', () => {
  it('calls subscribed events', () => {
    class StoreFixture extends FluxEventHubStore {
      execOnDispatch(foo, bar) {
      }

      getSubscribedEvents() {
        return [
          {
            name: 'event_name',
            callback: this.execOnDispatch,
            params: [
              'foo',
              'bar'
            ]
          }
        ];
      }
    }

    const foo = {};
    const bar = 'hello!';

    const instance = new StoreFixture();

    sinon.stub(instance, 'execOnDispatch', (fooParam, barParam) => {
      chai.expect(fooParam).to.equal(foo);
      chai.expect(barParam).to.equal(bar);
    });

    instance.init();

    const payload = {
      event: 'event_name',
      foo,
      bar
    };

    AppDispatcher.dispatch(payload);

    sinon.assert.calledOnce(instance.execOnDispatch);

    instance.execOnDispatch.restore();
  });

  it('misses event name', () => {
    class StoreFixture extends FluxEventHubStore {
      getSubscribedEvents() {
        return [{}];
      }
    }

    const instance = new StoreFixture();

    chai.expect(() => {
      instance.init();

      AppDispatcher.dispatch({});
    }).to.throw('Missing parameter "event" on dispatching payload!');
  });

  it('misses object parameter', () => {
    class StoreFixture extends FluxEventHubStore {
      getSubscribedEvents() {
        return [{
          name: 'foo',
          params: ['foo']
        }];
      }
    }

    const instance = new StoreFixture();
    instance.init();

    chai.expect(() => {
      AppDispatcher.dispatch({
        event: 'foo'
      });
    }).to.throw('Parameter "foo" is missing in event payload!');
  });
});
