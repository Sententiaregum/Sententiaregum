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

import chai from 'chai';
import sinon from 'sinon';
import axios from 'axios';
import AccountWebAPIUtils from '../../../util/api/AccountWebAPIUtils';

describe('AccountWebAPIUtils', () => {
  it('creates account', () => {
    const response = {
      id: Math.random()
    };

    const handler = sinon.spy();
    const promise = {
      then: function (handler) {
        handler.apply(this, [response]);
        return this;
      },
      catch: function () {
        return this;
      }
    };

    sinon.stub(axios, 'post', (url, data) => {
      chai.expect(data.username).to.equal('Ma27');
      chai.expect(url).to.equal('/api/users.json');
      return promise;
    });

    AccountWebAPIUtils.createAccount({ username: 'Ma27' }, handler, function () {});
    sinon.assert.calledOnce(handler);

    axios.post.restore();
  });

  it('activates user', () => {
    const response = {
      id: Math.random()
    };

    const handler = sinon.spy();
    const promise = {
      then: function (handler) {
        handler.apply(this, [response]);
        return this;
      },
      catch: function () {
        return this;
      }
    };

    sinon.stub(axios, 'patch', (url) => {
      chai.expect(url).to.equal('/api/users/activate.json?username=Ma27&activation_key=foo_key');
      return promise;
    });

    AccountWebAPIUtils.activate('Ma27', 'foo_key', handler, function () {});
    sinon.assert.calledOnce(handler);

    axios.patch.restore();
  });
});
