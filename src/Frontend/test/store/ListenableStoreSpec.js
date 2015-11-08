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

import ListenerStore from '../../store/ListenableStore';
import sinon from 'sinon';
import chai from 'chai';

let ListenableStore = new ListenerStore;

describe('ListenableStore', () => {
  it('dispatches change events on stores', () => {
    let callback = sinon.spy();

    ListenableStore.addChangeListener(callback, 'spec');
    ListenableStore.emitChange('spec');

    chai.assert(callback.called);

    ListenableStore.removeChangeListener(callback, 'spec');
    ListenableStore.emitChange('spec');

    chai.assert(callback.calledOnce);
  });
});
