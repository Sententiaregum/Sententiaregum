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

import getStateValue from '../../../store/provider/getStateValue';
import { expect } from 'chai';

describe('getStateValue', () => {
  it('fetches value from state', () => {
    expect(getStateValue(new StoreMock(), 'foo')).to.equal('bar');
  });

  it('returns default value', () => {
    expect(getStateValue(new StoreMock(), 'blah', 'default')).to.equal('default');
  });

  it('returns falsy data properly', () => {
    expect(getStateValue(new StoreMock(), 'falsy', true)).to.equal(false);
  })
});

class StoreMock {
  getState() {
    return { foo: 'bar', falsy: false };
  }
}
