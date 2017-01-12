/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian@mbosch.me>
 * (c) Ben Bieler <ben@benbieler.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

'use strict';

import { expect } from 'chai';
import FormValueContainer from '../../../util/react/FormValueContainer';
import { stub } from 'sinon';

describe('FormValueContainer', () => {
  let instance;
  beforeEach(() => {
    instance = new FormValueContainer();
  });

  it('can set/get form values', () => {
    instance.persistFormValue('prefix.value', 'blah');
    expect(instance.getFormValueForAlias('prefix.value')).to.equal('blah');
    localStorage.removeItem('prefix.value');
  });

  it('purges multiple form items', () => {
    stub(localStorage, 'removeItem');
    localStorage['prefix.bar'] = 'foo';

    instance.purge('prefix');
    expect(localStorage.removeItem.calledWith('prefix.bar'));
    localStorage.removeItem.restore();
    delete localStorage['prefix.bar'];
  });
});
